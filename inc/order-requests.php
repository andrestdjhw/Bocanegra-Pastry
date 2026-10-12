<?php
/**
 * Solicitudes de pedido.
 *
 * Pedido = solicitud, no compra: no hay carrito, checkout ni datos de pago.
 * - Registro interno (CPT privado) con estado New / Confirmed / Completed / Cancelled.
 * - Endpoint REST con validación en servidor (mínimo now + 24 h en America/Los_Angeles).
 * - Antispam: honeypot, tiempo mínimo de llenado y límite por IP.
 * - Notificación al negocio por wp_mail (SMTP) con reintentos; autorespuesta al cliente.
 */

const BN_ORDER_CPT      = 'bn_order_request';
const BN_ORDER_TZ       = 'America/Los_Angeles';
const BN_ORDER_MAX_QTY  = 50;
const BN_ORDER_CONSENT  = 'I agree to be contacted about my order request.';

function bn_order_statuses() {
  return array(
    'new'       => 'New',
    'confirmed' => 'Confirmed',
    'completed' => 'Completed',
    'cancelled' => 'Cancelled',
  );
}

/* ─── Registro y permisos ─────────────────────────────────────── */

function bn_register_order_cpt() {
  register_post_type(BN_ORDER_CPT, array(
    'labels' => array(
      'name'               => 'Order Requests',
      'singular_name'      => 'Order Request',
      'menu_name'          => 'Order Requests',
      'edit_item'          => 'Order Request',
      'search_items'       => 'Search Requests',
      'not_found'          => 'No order requests yet.',
      'not_found_in_trash' => 'No order requests in trash.',
    ),
    'public'              => false,
    'publicly_queryable'  => false,
    'exclude_from_search' => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_rest'        => false,
    'query_var'           => false,
    'rewrite'             => false,
    'menu_position'       => 25,
    'menu_icon'           => 'dashicons-clipboard',
    'supports'            => false,
    'capability_type'     => array('bn_order', 'bn_orders'),
    'map_meta_cap'        => true,
    'capabilities'        => array('create_posts' => 'do_not_allow'),
  ));
}

add_action('init', 'bn_register_order_cpt');

/**
 * Solo administradores y editores pueden ver solicitudes.
 */
function bn_grant_order_caps() {
  if (get_option('bn_order_caps_version') === '1') {
    return;
  }

  $caps = array(
    'edit_bn_orders', 'edit_others_bn_orders', 'edit_published_bn_orders', 'edit_private_bn_orders',
    'publish_bn_orders', 'read_private_bn_orders',
    'delete_bn_orders', 'delete_others_bn_orders', 'delete_published_bn_orders', 'delete_private_bn_orders',
  );

  foreach (array('administrator', 'editor') as $role_name) {
    $role = get_role($role_name);
    if ($role) {
      foreach ($caps as $cap) {
        $role->add_cap($cap);
      }
    }
  }

  update_option('bn_order_caps_version', '1');
}

add_action('admin_init', 'bn_grant_order_caps');

/* ─── Endpoint REST ───────────────────────────────────────────── */

function bn_register_order_route() {
  register_rest_route('bocanegra/v1', '/order-requests', array(
    'methods'             => WP_REST_Server::CREATABLE,
    'callback'            => 'bn_handle_order_request',
    'permission_callback' => '__return_true',
  ));
}

add_action('rest_api_init', 'bn_register_order_route');

function bn_handle_order_request(WP_REST_Request $request) {
  $params = $request->get_json_params();
  if (!is_array($params)) {
    $params = array();
  }

  // Honeypot: los bots reciben "éxito" pero no se guarda nada.
  if (!empty($params['company'])) {
    return new WP_REST_Response(array('ok' => true), 201);
  }

  if ((int) ($params['elapsed_ms'] ?? 0) < 3000) {
    return new WP_Error('bn_too_fast', 'Please take a moment to review your request and try again.', array('status' => 400));
  }

  $rate_key = 'bn_rl_' . md5($_SERVER['REMOTE_ADDR'] ?? '');
  $sent     = (int) get_transient($rate_key);
  if ($sent >= 5) {
    return new WP_Error('bn_rate_limited', 'You’ve sent several requests in a short time. Please try again later.', array('status' => 429));
  }

  list($data, $errors) = bn_validate_order($params);

  if ($errors) {
    return new WP_Error('bn_invalid', 'Please review the highlighted fields.', array('status' => 422, 'fields' => $errors));
  }

  set_transient($rate_key, $sent + 1, HOUR_IN_SECONDS);

  $post_id = wp_insert_post(array(
    'post_type'   => BN_ORDER_CPT,
    'post_status' => 'publish',
    'post_title'  => sprintf('%s — %s %s', $data['name'], $data['date'], $data['time']),
  ), true);

  if (is_wp_error($post_id)) {
    return new WP_Error('bn_save_failed', 'Something went wrong while sending your request. Please try again in a few minutes.', array('status' => 500));
  }

  foreach ($data as $field => $value) {
    update_post_meta($post_id, '_bn_' . $field, $value);
  }

  update_post_meta($post_id, '_bn_status', 'new');
  update_post_meta($post_id, '_bn_consent_text', BN_ORDER_CONSENT);
  update_post_meta($post_id, '_bn_consent_at', gmdate('c'));
  update_post_meta($post_id, '_bn_mail_attempts', 2);

  // La confirmación al cliente solo se muestra si el negocio recibió el correo.
  if (!bn_send_business_notification($post_id) && !bn_send_business_notification($post_id)) {
    update_post_meta($post_id, '_bn_mail_status', 'failed');
    wp_schedule_single_event(time() + 5 * MINUTE_IN_SECONDS, 'bn_retry_order_mail', array($post_id));

    return new WP_Error('bn_mail_failed', 'Something went wrong while sending your request. Please try again in a few minutes.', array('status' => 503));
  }

  update_post_meta($post_id, '_bn_mail_status', 'sent');
  bn_send_customer_autoreply($post_id);

  return new WP_REST_Response(array('ok' => true), 201);
}

/**
 * @return array{0: array, 1: array} Datos limpios y errores por campo.
 */
function bn_validate_order(array $p) {
  $errors = array();

  $name = sanitize_text_field($p['name'] ?? '');
  if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
    $errors['name'] = 'Please enter your full name.';
  }

  $email = sanitize_email($p['email'] ?? '');
  if (!is_email($email)) {
    $errors['email'] = 'Please enter a valid email address.';
  }

  $phone  = sanitize_text_field($p['phone'] ?? '');
  $digits = preg_replace('/\D/', '', $phone);
  if (strlen($digits) < 10 || strlen($digits) > 15) {
    $errors['phone'] = 'Please enter a valid phone number, including area code.';
  }

  $date = sanitize_text_field($p['date'] ?? '');
  $time = sanitize_text_field($p['time'] ?? '');
  $when = DateTimeImmutable::createFromFormat('!Y-m-d H:i', $date . ' ' . $time, new DateTimeZone(BN_ORDER_TZ));

  if (!$date) {
    $errors['date'] = 'Please choose your preferred date.';
  } elseif (!$time) {
    $errors['time'] = 'Please choose your preferred time.';
  } elseif (!$when || $when->format('Y-m-d H:i') !== $date . ' ' . $time) {
    $errors['date'] = 'Please choose a valid date and time.';
  } elseif ($when->getTimestamp() < time() + DAY_IN_SECONDS - 2 * MINUTE_IN_SECONDS) {
    // 2 minutos de margen solo para la latencia del envío.
    $errors['time'] = 'Please choose a date and time at least 24 hours from now (Pacific Time).';
  } elseif ($when->getTimestamp() > time() + YEAR_IN_SECONDS) {
    $errors['date'] = 'Please choose a date within the next 12 months.';
  }

  $quantity = filter_var($p['quantity'] ?? null, FILTER_VALIDATE_INT, array(
    'options' => array('min_range' => 1, 'max_range' => BN_ORDER_MAX_QTY),
  ));
  if ($quantity === false) {
    $errors['quantity'] = sprintf('Please enter a number between 1 and %d.', BN_ORDER_MAX_QTY);
  }

  $notes = sanitize_textarea_field($p['notes'] ?? '');
  if (mb_strlen($notes) > 1000) {
    $errors['notes'] = 'Please keep your message under 1,000 characters.';
  }

  if (($p['consent'] ?? false) !== true) {
    $errors['consent'] = 'Please confirm we may contact you about your request.';
  }

  $source = in_array($p['source'] ?? '', array('home', 'page'), true) ? $p['source'] : 'page';

  return array(
    compact('name', 'email', 'phone', 'date', 'time', 'quantity', 'notes', 'source'),
    $errors,
  );
}

/* ─── Correo ──────────────────────────────────────────────────── */

function bn_order_data($post_id) {
  $get  = function ($key) use ($post_id) {
    return get_post_meta($post_id, '_bn_' . $key, true);
  };
  $when = DateTimeImmutable::createFromFormat('!Y-m-d H:i', $get('date') . ' ' . $get('time'), new DateTimeZone(BN_ORDER_TZ));

  return array(
    'name'       => $get('name'),
    'email'      => $get('email'),
    'phone'      => $get('phone'),
    'when_label' => $when ? $when->format('l, F j, Y \a\t g:i A') . ' (Pacific Time)' : $get('date') . ' ' . $get('time'),
    'quantity'   => (int) $get('quantity'),
    'notes'      => $get('notes'),
    'source'     => $get('source') === 'home' ? 'Home page form' : 'Request an Order page',
    'status'     => $get('status') ?: 'new',
    'mail'       => $get('mail_status'),
    'consent_at' => $get('consent_at'),
  );
}

function bn_send_business_notification($post_id) {
  $order = bn_order_data($post_id);
  $to    = bn_config()['notify_email'] ?: get_option('admin_email');

  $body = implode("\n", array(
    'A new cheesecake request was submitted on the website.',
    'It is NOT confirmed until someone from the team contacts the customer.',
    '',
    'Name: ' . $order['name'],
    'Email: ' . $order['email'],
    'Phone: ' . $order['phone'],
    'Preferred date & time: ' . $order['when_label'],
    'Number of cheesecakes: ' . $order['quantity'],
    'Questions or special requests: ' . ($order['notes'] ?: '—'),
    'Submitted from: ' . $order['source'],
    '',
    'Manage this request:',
    admin_url('post.php?post=' . $post_id . '&action=edit'),
  ));

  $headers = array(sprintf('Reply-To: "%s" <%s>', str_replace('"', '', $order['name']), $order['email']));

  return wp_mail($to, sprintf('New cheesecake request: %s (%s)', $order['name'], $order['when_label']), $body, $headers);
}

function bn_send_customer_autoreply($post_id) {
  $order  = bn_order_data($post_id);
  $config = bn_config();

  $lines = array(
    'Hi ' . $order['name'] . ',',
    '',
    'Thank you! We’ve received your cheesecake request.',
    'Your order is not yet confirmed. Our team will contact you to confirm availability and next steps.',
    '',
    'Your request:',
    '- Preferred date & time: ' . $order['when_label'],
    '- Number of cheesecakes: ' . $order['quantity'],
  );

  if ($order['notes']) {
    $lines[] = '- Your note: ' . $order['notes'];
  }

  array_push($lines, '', $config['slogan'], $config['brand'], $config['area']);

  return wp_mail($order['email'], 'We received your Bocanegra cheesecake request', implode("\n", $lines));
}

/**
 * Reintentos ante falla SMTP (hasta 5 intentos en total, con espera creciente).
 */
function bn_retry_order_mail($post_id) {
  if (get_post_meta($post_id, '_bn_mail_status', true) !== 'failed') {
    return;
  }

  $attempts = (int) get_post_meta($post_id, '_bn_mail_attempts', true) + 1;
  update_post_meta($post_id, '_bn_mail_attempts', $attempts);

  if (bn_send_business_notification($post_id)) {
    update_post_meta($post_id, '_bn_mail_status', 'sent');
    return;
  }

  if ($attempts < 5) {
    wp_schedule_single_event(time() + $attempts * 15 * MINUTE_IN_SECONDS, 'bn_retry_order_mail', array($post_id));
  }
}

add_action('bn_retry_order_mail', 'bn_retry_order_mail');

function bn_log_mail_failure($error) {
  error_log('[Bocanegra] wp_mail failed: ' . $error->get_error_message());
}

add_action('wp_mail_failed', 'bn_log_mail_failure');

/**
 * SMTP opcional por constantes en wp-config.php (o usar un plugin como WP Mail SMTP):
 * BN_SMTP_HOST, BN_SMTP_PORT, BN_SMTP_USER, BN_SMTP_PASS, BN_SMTP_SECURE ('tls'|'ssl'), BN_MAIL_FROM.
 */
function bn_configure_smtp($mailer) {
  if (!defined('BN_SMTP_HOST')) {
    return;
  }

  $mailer->isSMTP();
  $mailer->Host       = BN_SMTP_HOST;
  $mailer->Port       = defined('BN_SMTP_PORT') ? BN_SMTP_PORT : 587;
  $mailer->SMTPSecure = defined('BN_SMTP_SECURE') ? BN_SMTP_SECURE : 'tls';
  $mailer->SMTPAuth   = defined('BN_SMTP_USER');

  if ($mailer->SMTPAuth) {
    $mailer->Username = BN_SMTP_USER;
    $mailer->Password = defined('BN_SMTP_PASS') ? BN_SMTP_PASS : '';
  }
}

add_action('phpmailer_init', 'bn_configure_smtp');

add_filter('wp_mail_from', function ($from) {
  return defined('BN_MAIL_FROM') ? BN_MAIL_FROM : $from;
});

add_filter('wp_mail_from_name', function ($name) {
  return $name === 'WordPress' ? bn_config()['brand'] : $name;
});

/* ─── Panel de administración ─────────────────────────────────── */

function bn_order_columns() {
  return array(
    'cb'          => '<input type="checkbox" />',
    'bn_customer' => 'Customer',
    'bn_contact'  => 'Contact',
    'bn_when'     => 'Preferred date & time',
    'bn_quantity' => 'Qty',
    'bn_status'   => 'Status',
    'date'        => 'Received',
  );
}

add_filter('manage_' . BN_ORDER_CPT . '_posts_columns', 'bn_order_columns');

function bn_order_status_badge($status) {
  $colors = array('new' => '#b08d57', 'confirmed' => '#2f6f4f', 'completed' => '#4a2e1c', 'cancelled' => '#8a8a8a');
  $labels = bn_order_statuses();

  return sprintf(
    '<span style="display:inline-block;padding:2px 10px;border-radius:999px;color:#fff;background:%s;font-weight:600">%s</span>',
    esc_attr($colors[$status] ?? '#8a8a8a'),
    esc_html($labels[$status] ?? $status)
  );
}

function bn_render_order_column($column, $post_id) {
  $order = bn_order_data($post_id);

  switch ($column) {
    case 'bn_customer':
      printf('<strong><a class="row-title" href="%s">%s</a></strong>', esc_url(get_edit_post_link($post_id)), esc_html($order['name']));
      if ($order['mail'] === 'failed') {
        echo '<br><span style="color:#b32d2e">Notification email failed</span>';
      }
      break;
    case 'bn_contact':
      printf('<a href="mailto:%1$s">%1$s</a><br>%2$s', esc_attr($order['email']), esc_html($order['phone']));
      break;
    case 'bn_when':
      echo esc_html($order['when_label']);
      break;
    case 'bn_quantity':
      echo esc_html($order['quantity']);
      break;
    case 'bn_status':
      echo bn_order_status_badge($order['status']);
      break;
  }
}

add_action('manage_' . BN_ORDER_CPT . '_posts_custom_column', 'bn_render_order_column', 10, 2);

add_filter('list_table_primary_column', function ($default, $screen) {
  return $screen === 'edit-' . BN_ORDER_CPT ? 'bn_customer' : $default;
}, 10, 2);

add_filter('post_row_actions', function ($actions, $post) {
  if ($post->post_type === BN_ORDER_CPT) {
    unset($actions['inline hide-if-no-js'], $actions['view']);
  }
  return $actions;
}, 10, 2);

function bn_order_status_filter($post_type) {
  if ($post_type !== BN_ORDER_CPT) {
    return;
  }

  $current = sanitize_key($_GET['bn_status'] ?? '');
  echo '<select name="bn_status"><option value="">All statuses</option>';
  foreach (bn_order_statuses() as $value => $label) {
    printf('<option value="%s"%s>%s</option>', esc_attr($value), selected($current, $value, false), esc_html($label));
  }
  echo '</select>';
}

add_action('restrict_manage_posts', 'bn_order_status_filter');

function bn_order_status_query($query) {
  if (!is_admin() || !$query->is_main_query() || $query->get('post_type') !== BN_ORDER_CPT) {
    return;
  }

  $status = sanitize_key($_GET['bn_status'] ?? '');
  if ($status && isset(bn_order_statuses()[$status])) {
    $query->set('meta_query', array(array('key' => '_bn_status', 'value' => $status)));
  }
}

add_action('pre_get_posts', 'bn_order_status_query');

function bn_order_meta_boxes() {
  add_meta_box('bn_order_details', 'Request Details', 'bn_render_order_details', BN_ORDER_CPT, 'normal', 'high');
  add_meta_box('bn_order_status', 'Order Status', 'bn_render_order_status', BN_ORDER_CPT, 'side', 'high');
}

add_action('add_meta_boxes_' . BN_ORDER_CPT, 'bn_order_meta_boxes');

function bn_render_order_details($post) {
  $order = bn_order_data($post->ID);
  $rows  = array(
    'Name'                          => esc_html($order['name']),
    'Email'                         => sprintf('<a href="mailto:%1$s">%1$s</a>', esc_attr($order['email'])),
    'Phone'                         => sprintf('<a href="tel:%s">%s</a>', esc_attr(preg_replace('/[^\d+]/', '', $order['phone'])), esc_html($order['phone'])),
    'Preferred date & time'         => esc_html($order['when_label']),
    'Number of cheesecakes'         => esc_html($order['quantity']),
    'Questions or special requests' => $order['notes'] ? nl2br(esc_html($order['notes'])) : '—',
    'Submitted from'                => esc_html($order['source']),
    'Received'                      => esc_html(get_the_date('F j, Y g:i A', $post)),
    'Contact consent'               => $order['consent_at'] ? esc_html('Yes — ' . BN_ORDER_CONSENT) : 'No',
    'Notification email'            => $order['mail'] === 'sent' ? 'Sent' : '<span style="color:#b32d2e">Failed — retrying automatically</span>',
  );

  echo '<table class="widefat striped"><tbody>';
  foreach ($rows as $label => $value) {
    printf('<tr><th style="width:220px">%s</th><td>%s</td></tr>', esc_html($label), $value);
  }
  echo '</tbody></table>';
}

function bn_render_order_status($post) {
  $current = get_post_meta($post->ID, '_bn_status', true) ?: 'new';
  wp_nonce_field('bn_order_status', 'bn_order_status_nonce');

  echo '<select name="bn_status_value" style="width:100%">';
  foreach (bn_order_statuses() as $value => $label) {
    printf('<option value="%s"%s>%s</option>', esc_attr($value), selected($current, $value, false), esc_html($label));
  }
  echo '</select><p class="description">Click “Update” to save the status.</p>';
}

function bn_save_order_status($post_id) {
  if (
    !isset($_POST['bn_order_status_nonce']) ||
    !wp_verify_nonce($_POST['bn_order_status_nonce'], 'bn_order_status') ||
    (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) ||
    !current_user_can('edit_post', $post_id)
  ) {
    return;
  }

  $status = sanitize_key($_POST['bn_status_value'] ?? '');
  if (isset(bn_order_statuses()[$status])) {
    update_post_meta($post_id, '_bn_status', $status);
  }
}

add_action('save_post_' . BN_ORDER_CPT, 'bn_save_order_status');

/**
 * Alerta en el escritorio cuando alguna notificación no pudo enviarse.
 */
function bn_mail_failure_notice() {
  if (!current_user_can('edit_bn_orders')) {
    return;
  }

  $failed = get_posts(array(
    'post_type'      => BN_ORDER_CPT,
    'posts_per_page' => -1,
    'fields'         => 'ids',
    'meta_key'       => '_bn_mail_status',
    'meta_value'     => 'failed',
  ));

  if ($failed) {
    printf(
      '<div class="notice notice-error"><p><strong>Bocanegra:</strong> %d order request(s) could not be emailed to the business. <a href="%s">Review Order Requests</a> and check the SMTP settings.</p></div>',
      count($failed),
      esc_url(admin_url('edit.php?post_type=' . BN_ORDER_CPT))
    );
  }
}

add_action('admin_notices', 'bn_mail_failure_notice');

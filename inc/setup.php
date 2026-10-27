<?php
/**
 * Soporte del tema, assets y datos compartidos con los componentes React.
 */

function bn_add_support() {
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_theme_support('html5', array('search-form', 'gallery', 'caption', 'style', 'script'));
}

add_action('after_setup_theme', 'bn_add_support');

function bn_load_assets() {
  $asset = include get_theme_file_path('build/index.asset.php');

  wp_enqueue_style('bocanegra-main', get_theme_file_uri('/build/index.css'), array(), $asset['version']);
  wp_enqueue_script('bocanegra-main', get_theme_file_uri('/build/index.js'), $asset['dependencies'], $asset['version'], true);
  wp_add_inline_script('bocanegra-main', 'window.bocanegraData = ' . wp_json_encode(bn_script_data()) . ';', 'before');
}

add_action('wp_enqueue_scripts', 'bn_load_assets');

/**
 * Precarga las fuentes que pintan primero (títulos y párrafos) para evitar saltos de texto.
 */
function bn_preload_fonts() {
  foreach (array('big-shoulders-60-700', 'lato-400') as $font) {
    printf("<link rel=\"preload\" href=\"%s\" as=\"font\" type=\"font/woff2\" crossorigin>\n", esc_url(get_theme_file_uri('assets/fonts/' . $font . '.woff2')));
  }
}

add_action('wp_head', 'bn_preload_fonts', 1);

/**
 * Precarga el poster del video del hero (es la imagen principal / LCP de la Home).
 */
function bn_preload_hero_poster() {
  if (!is_front_page()) {
    return;
  }

  $poster = bn_find_image('home-hero-poster');
  if ($poster) {
    printf("<link rel=\"preload\" as=\"image\" href=\"%s\" fetchpriority=\"high\">\n", esc_url(bn_versioned_uri(isset($poster['webp']) ? $poster['webp'] : $poster['base'])));
  }
}

add_action('wp_head', 'bn_preload_hero_poster', 1);

/**
 * Favicon con el isotipo oficial, mientras no se configure un Site Icon en WordPress.
 */
function bn_favicons() {
  if (has_site_icon()) {
    return;
  }

  printf("<link rel=\"icon\" href=\"%s\" sizes=\"32x32\">\n", esc_url(bn_brand_url('favicon-32', 'png')));
  printf("<link rel=\"icon\" href=\"%s\" sizes=\"192x192\">\n", esc_url(bn_brand_url('favicon-192', 'png')));
  printf("<link rel=\"apple-touch-icon\" href=\"%s\">\n", esc_url(bn_brand_url('apple-touch-icon', 'png')));
}

add_action('wp_head', 'bn_favicons', 2);

/**
 * Datos que consumen Navbar, Footer y OrderRequestForm.
 */
function bn_script_data() {
  $config = bn_config();
  $nav    = array();

  foreach (bn_pages() as $key => $page) {
    $nav[] = array('key' => $key, 'label' => $page['nav'], 'href' => bn_url($key));
  }

  $labels = array('instagram' => 'Instagram', 'facebook' => 'Facebook', 'tiktok' => 'TikTok');
  $social = array();
  foreach ($config['social'] as $network => $url) {
    if (isset($labels[$network])) {
      $social[] = array('network' => $network, 'label' => $labels[$network], 'href' => $url ? esc_url_raw($url) : '');
    }
  }

  return array(
    'brand'      => $config['brand'],
    'slogan'     => $config['slogan'],
    'area'       => $config['area'],
    'focus'      => $config['focus'],
    'homeUrl'    => home_url('/'),
    'logos'      => array(
      'header' => bn_brand_url('logo-script-chocolate'),
      'footer' => bn_brand_url('isologo-cream'),
    ),
    'current'    => bn_current_page_key(),
    'nav'        => $nav,
    'cta'        => array('label' => 'Request a Cheesecake', 'href' => bn_url('request')),
    'contact'    => array(
      'phone'        => $config['phone'],
      'phoneDisplay' => $config['phone_display'] ?: $config['phone'],
      'email'        => $config['email'],
    ),
    'social'     => $social,
    'brandAssets' => array(
      'watermark' => bn_brand_url('isologo-cream'),
      'slice'     => bn_brand_url('rebanada-lisa-light'),
    ),
    'privacyUrl' => get_privacy_policy_url(),
    'termsUrl'   => bn_terms_url(),
    'form'       => array(
      'endpoint'         => rest_url('bocanegra/v1/order-requests'),
      'maxQuantity'      => BN_ORDER_MAX_QTY,
      'recaptchaSiteKey' => $config['recaptcha_site_key'],
      'emailjs'          => bn_emailjs_enabled() ? array(
        'serviceId'  => $config['emailjs']['service_id'],
        'templateId' => $config['emailjs']['template_id'],
        'autoReplyTemplateId' => $config['emailjs']['autoreply_template_id'] ?? '',
        'publicKey'  => $config['emailjs']['public_key'],
      ) : null,
    ),
  );
}

/**
 * Crea las 4 páginas del brief (una sola vez) y las asigna como portada.
 * Las plantillas page-{slug}.php y front-page.php se encargan del contenido.
 */
function bn_ensure_pages() {
  if (get_option('bn_pages_version') === '1') {
    return;
  }

  foreach (bn_pages() as $key => $page) {
    $existing = get_page_by_path($page['slug']);
    $page_id  = $existing ? $existing->ID : wp_insert_post(array(
      'post_type'   => 'page',
      'post_status' => 'publish',
      'post_title'  => $page['title'],
      'post_name'   => $page['slug'],
    ));

    if ($key === 'home' && $page_id && !is_wp_error($page_id)) {
      update_option('show_on_front', 'page');
      update_option('page_on_front', $page_id);
    }
  }

  if (!get_option('permalink_structure')) {
    update_option('permalink_structure', '/%postname%/');
    flush_rewrite_rules(false);
  }

  update_option('bn_pages_version', '1');
}

add_action('init', 'bn_ensure_pages', 20);

/**
 * Activa las animaciones de scroll antes del primer pintado (evita parpadeo).
 * Si el JS principal no carga en 3 s, se quitan para que nunca quede contenido oculto.
 */
function bn_animation_flag() {
  ?>
  <script>(function(d){if(window.matchMedia&&matchMedia('(prefers-reduced-motion: reduce)').matches)return;var h=d.documentElement;h.classList.add('bn-anim');setTimeout(function(){if(!window.bnAnimReady)h.classList.remove('bn-anim');},3000);})(document);</script>
  <?php
}

add_action('wp_head', 'bn_animation_flag', 0);

/**
 * GTM: solo se carga cuando hay un contenedor configurado.
 */
function bn_gtm_head() {
  $id = bn_config()['gtm_id'];
  if (!$id) {
    return;
  }
  ?>
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?php echo esc_js($id); ?>');</script>
  <?php
}

add_action('wp_head', 'bn_gtm_head', 1);

function bn_gtm_body() {
  $id = bn_config()['gtm_id'];
  if ($id) {
    printf('<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=%s" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>', esc_attr($id));
  }
}

add_action('wp_body_open', 'bn_gtm_body');

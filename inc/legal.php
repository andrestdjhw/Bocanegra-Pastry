<?php
/**
 * Páginas legales: Privacy Policy y Terms and Conditions.
 *
 * Se crean una sola vez (opción bn_legal_pages_version) con un texto inicial que describe
 * cómo funciona realmente el sitio: formulario de solicitud, WordPress, EmailJS, Google reCAPTCHA v3
 * y Google Maps. Después quedan editables en WordPress y el tema no las vuelve a sobrescribir.
 * IMPORTANTE: borrador para revisión legal antes del lanzamiento.
 *
 * Shortcodes para datos que aún no están confirmados (se actualizan solos desde bn_config()):
 * [bn_contact], [bn_analytics_notice].
 */

const BN_LEGAL_UPDATED = 'October 27, 2026';

/**
 * Cómo contactarnos: correo/teléfono verificados o, mientras no existan, el formulario.
 */
function bn_contact_shortcode() {
  $config = bn_config();
  $ways   = array();

  if ($config['email']) {
    $ways[] = sprintf('by email at <a href="mailto:%1$s">%1$s</a>', esc_attr($config['email']));
  }
  if ($config['phone']) {
    $ways[] = sprintf('by phone at <a href="tel:%s">%s</a>', esc_attr($config['phone']), esc_html($config['phone_display'] ?: $config['phone']));
  }

  $ways[] = sprintf('through the request form on our <a href="%s">Request an Order</a> page (mention your privacy or terms question in the notes)', esc_url(bn_url('request')));

  return 'You can contact ' . esc_html($config['brand']) . ' ' . implode(', or ', $ways) . '.';
}

add_shortcode('bn_contact', 'bn_contact_shortcode');

/**
 * Párrafo de analítica: solo aparece si se configura Google Tag Manager.
 */
function bn_analytics_notice_shortcode() {
  if (!bn_config()['gtm_id']) {
    return '';
  }

  return '<h3>Website analytics</h3><p>We use Google Tag Manager to load analytics tools that help us understand how visitors use our website (for example, which pages are visited and whether a request form was submitted). These tools may set cookies and collect information such as your IP address, device and browser details, and pages viewed. You can limit cookies through your browser settings.</p>';
}

add_shortcode('bn_analytics_notice', 'bn_analytics_notice_shortcode');

function bn_privacy_policy_content() {
  $brand = esc_html(bn_config()['brand']);

  return <<<HTML
<p><em>Last updated: {BN_UPDATED}</em></p>

<p>This Privacy Policy explains how {$brand} (“Bocanegra,” “we,” “us”) collects, uses, and shares information when you visit our website or submit a cheesecake request. We are a small, family-run cheesecake business based in Orange County, California.</p>

<h2>Information we collect</h2>
<h3>Information you provide</h3>
<p>When you submit a cheesecake request, we collect:</p>
<ul>
<li>Your full name, email address, and phone number</li>
<li>Your preferred date and time and the number of cheesecakes</li>
<li>Any questions or special requests you choose to include</li>
<li>Your consent to be contacted about your request, and the date and time it was given</li>
</ul>
<p>We do not collect payment card information through our website. No purchase or payment takes place online.</p>

<h3>Information collected automatically</h3>
<ul>
<li><strong>Spam protection.</strong> To prevent automated or repeated submissions, our server temporarily uses your IP address to limit the number of requests sent in a short period. This limit expires after one hour.</li>
<li><strong>Google reCAPTCHA.</strong> When you start filling out the request form, the page loads Google reCAPTCHA v3, which collects information such as your IP address, device and browser details, and how you interact with the page, and sends it to Google to help determine whether the request comes from a person. See “Service providers” below.</li>
<li><strong>EmailJS.</strong> To prevent repeated submissions, the EmailJS tool may store the time of your last request in your browser’s local storage.</li>
<li><strong>Google Maps.</strong> Our pages include an embedded Google Map of the Orange County area. Google may collect information such as your IP address and may set cookies when the map loads or when you interact with it.</li>
</ul>
[bn_analytics_notice]

<h2>How we use your information</h2>
<ul>
<li>To review your cheesecake request and contact you about availability and order details</li>
<li>To send you a confirmation that we received your request</li>
<li>To keep an internal record of requests and their status</li>
<li>To protect our website and request form from spam and abuse</li>
</ul>
<p>Submitting a request does not confirm an order. We use your contact information only to respond to your request and arrange your order. We do not sell your personal information and we do not use it for unrelated marketing without your permission.</p>

<h2>Service providers</h2>
<p>We share information only with the service providers we need to run our website and respond to your request:</p>
<ul>
<li><strong>EmailJS</strong> (<a href="https://www.emailjs.com/legal/privacy-policy/" target="_blank" rel="noopener">EmailJS Privacy Policy</a>) — delivers the details of your request to our team by email, and may send you an automatic confirmation.</li>
<li><strong>Google reCAPTCHA</strong> — helps protect our form from spam. Its use is subject to the <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">Google Privacy Policy</a> and <a href="https://policies.google.com/terms" target="_blank" rel="noopener">Google Terms of Service</a>.</li>
<li><strong>Google Maps</strong> — displays the map of our general service area, subject to the <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">Google Privacy Policy</a>.</li>
<li><strong>Our website hosting and email providers</strong> — store your request in our website system and deliver email messages.</li>
</ul>
<p>We may also disclose information if required by law or to protect the rights and safety of our business and customers.</p>

<h2>How long we keep your information</h2>
<p>We keep request records only as long as needed to respond to your request, arrange your order, and maintain reasonable business records. Access to these records is limited to authorized members of our team.</p>

<h2>Your choices and rights</h2>
<p>You may ask us to access, correct, or delete the personal information you submitted through our website. Depending on where you live, including California, you may have additional rights under applicable law. To make a request, contact us using the details below. We will not discriminate against you for exercising your privacy rights.</p>

<h2>Do Not Track</h2>
<p>Our website does not currently respond to “Do Not Track” browser signals.</p>

<h2>Children’s privacy</h2>
<p>Our website is intended for adults placing cheesecake requests. We do not knowingly collect personal information from children under 13 through our website. If you believe a child has submitted information to us, please contact us and we will delete it.</p>

<h2>Security</h2>
<p>We use reasonable measures to protect the information you submit, including encrypted connections and restricted access to request records. No method of transmission or storage is completely secure.</p>

<h2>Changes to this policy</h2>
<p>We may update this Privacy Policy from time to time. When we do, we will update the “Last updated” date at the top of this page.</p>

<h2>Contact us</h2>
<p>[bn_contact]</p>
HTML;
}

function bn_terms_content() {
  $brand = esc_html(bn_config()['brand']);

  return <<<HTML
<p><em>Last updated: {BN_UPDATED}</em></p>

<p>These Terms and Conditions apply to your use of the {$brand} website and to cheesecake requests submitted through it. By using our website or submitting a request, you agree to these terms.</p>

<h2>Cheesecake requests are not purchases</h2>
<ul>
<li>Our request form lets you ask for a made-to-order cheesecake. Submitting a request does not confirm an order, reserve a date, or create a purchase.</li>
<li>Your order is confirmed only after our team contacts you and confirms availability and order details.</li>
<li>Please submit requests at least 24 hours before your preferred date and time. We may not be able to accommodate every request.</li>
<li>No payment is collected through our website. Any pricing, payment, pickup or delivery arrangements, and cancellation terms will be communicated to you directly when your order is confirmed.</li>
</ul>

<h2>Information you provide</h2>
<p>Please provide accurate contact details so we can reach you about your request. Do not include payment card numbers or other sensitive information in the request form. How we handle your information is described in our <a href="[bn_privacy_url]">Privacy Policy</a>.</p>

<h2>Form protection and email delivery</h2>
<p>Our request form is protected by Google reCAPTCHA, and the <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">Google Privacy Policy</a> and <a href="https://policies.google.com/terms" target="_blank" rel="noopener">Google Terms of Service</a> apply. Request details are delivered to our team by email through EmailJS. Requests that appear automated or abusive may be blocked.</p>

<h2>Product information</h2>
<p>Every cheesecake is prepared to order from our family recipe. Product photos are for illustration, and presentation may vary. If you have questions about size, ingredients, or allergens, please include them with your request so our team can provide confirmed details before your order is confirmed.</p>

<h2>Service area</h2>
<p>We are based in Orange County, California, with a focus on Irvine and Mission Viejo. Order arrangements are confirmed individually, and we do not guarantee service to every location.</p>

<h2>Intellectual property</h2>
<p>The {$brand} name, logos, illustrations, photos, videos, and website content belong to {$brand} or are used with permission. Please do not copy or use them without our written permission.</p>

<h2>Third-party links and services</h2>
<p>Our website may link to or display content from third parties, such as Google Maps and social media platforms. We are not responsible for their content or practices, and their own terms and privacy policies apply.</p>

<h2>Disclaimer and limitation of liability</h2>
<p>Our website and its content are provided “as is.” To the fullest extent permitted by law, {$brand} is not liable for indirect or consequential damages arising from your use of the website. Nothing in these terms limits rights you have under applicable law that cannot be waived.</p>

<h2>Governing law</h2>
<p>These terms are governed by the laws of the State of California, without regard to its conflict-of-law rules.</p>

<h2>Changes to these terms</h2>
<p>We may update these Terms and Conditions from time to time. When we do, we will update the “Last updated” date at the top of this page.</p>

<h2>Contact us</h2>
<p>[bn_contact]</p>
HTML;
}

/**
 * URL de la Privacy Policy dentro de los Términos (se resuelve al mostrar la página).
 */
add_shortcode('bn_privacy_url', function () {
  return esc_url(get_privacy_policy_url() ?: home_url('/privacy-policy/'));
});

/**
 * URL de Terms and Conditions ('' si aún no existe).
 */
function bn_terms_url() {
  $page = get_page_by_path('terms-and-conditions');
  return ($page && $page->post_status === 'publish') ? get_permalink($page) : '';
}

/**
 * Crea/publica las páginas legales una sola vez. La Privacy Policy reutiliza la página
 * que WordPress crea por defecto (borrador) y la asigna en Ajustes → Privacidad.
 */
function bn_ensure_legal_pages() {
  if (get_option('bn_legal_pages_version') === '1') {
    return;
  }

  $privacy_id = (int) get_option('wp_page_for_privacy_policy');
  $privacy    = $privacy_id ? get_post($privacy_id) : null;
  $content    = str_replace('{BN_UPDATED}', BN_LEGAL_UPDATED, bn_privacy_policy_content());

  if ($privacy && $privacy->post_type === 'page' && $privacy->post_status !== 'trash') {
    wp_update_post(array(
      'ID'           => $privacy->ID,
      'post_title'   => 'Privacy Policy',
      'post_name'    => 'privacy-policy',
      'post_status'  => 'publish',
      'post_content' => $content,
    ));
  } else {
    $privacy_id = wp_insert_post(array(
      'post_type'    => 'page',
      'post_status'  => 'publish',
      'post_title'   => 'Privacy Policy',
      'post_name'    => 'privacy-policy',
      'post_content' => $content,
    ));
  }

  if ($privacy_id && !is_wp_error($privacy_id)) {
    update_option('wp_page_for_privacy_policy', $privacy_id);
  }

  if (!get_page_by_path('terms-and-conditions')) {
    wp_insert_post(array(
      'post_type'    => 'page',
      'post_status'  => 'publish',
      'post_title'   => 'Terms and Conditions',
      'post_name'    => 'terms-and-conditions',
      'post_content' => str_replace('{BN_UPDATED}', BN_LEGAL_UPDATED, bn_terms_content()),
    ));
  }

  update_option('bn_legal_pages_version', '1');
}

add_action('init', 'bn_ensure_legal_pages', 21);

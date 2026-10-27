<?php
/**
 * Configuración central del sitio.
 *
 * Regla del brief: solo publicar datos confirmados por el cliente.
 * Todo campo vacío ('' o []) simplemente no se renderiza en el sitio,
 * así que nunca quedan placeholders visibles.
 */

function bn_config() {
  static $config = null;

  if ($config !== null) {
    return $config;
  }

  $config = apply_filters('bn_config', array(
    'brand'  => 'Bocanegra Pastry Boutique',
    'slogan' => 'Every Slice Tells a Story.',
    'area'   => 'Orange County, California',
    'focus'  => array('Irvine', 'Mission Viejo'),

    // Pendiente: teléfono / email verificados.
    'phone'         => '',  // formato E.164, ej. +19495550000
    'phone_display' => '',  // ej. (949) 555-0000
    'email'         => '',

    // Destino de notificaciones de pedidos. Vacío = email del administrador de WordPress.
    // (Con EmailJS activo, el destinatario se define en la plantilla de EmailJS; este correo
    // se usa solo para el respaldo por wp_mail).
    'notify_email' => '',

    // EmailJS: envía la notificación del pedido desde el navegador. Estas claves son públicas por diseño.
    // Vaciar 'service_id' desactiva EmailJS y el sitio vuelve a enviar todo por wp_mail/SMTP.
    'emailjs' => array(
      'service_id'           => 'service_6ira9cq',
      'template_id'          => 'template_oclkset', // aviso al negocio
      'autoreply_template_id' => 'template_hp303rl', // confirmación al cliente ('' = sin auto-reply por EmailJS)
      'public_key'           => '73VP7PM69Atf30gyQ',
    ),

    // Google reCAPTCHA v3 (clave de sitio, pública). La verifica WordPress, no EmailJS
    // (EmailJS solo admite reCAPTCHA v2). La clave SECRETA va en wp-config.php:
    // define('BN_RECAPTCHA_SECRET', '...'); — sin ella, el formulario funciona sin verificación de reCAPTCHA.
    'recaptcha_site_key' => '6LcR3QMuAAAAAFKC-EQAYn2a76jPIf0gaKVuqRSw',

    // Redes sociales: pegar la URL verificada cuando exista la cuenta.
    // Sin URL, el ícono se muestra sin enlace (no se publican links rotos ni se agrega al schema).
    'social' => array(
      'instagram' => '',
      'facebook'  => '',
      'tiktok'    => '',
    ),

    // Reseñas reales con permiso. Si está vacío, el módulo de Home se omite por completo.
    // Ej. array(array('quote' => '...', 'author' => 'Maria G.', 'source' => 'Google'))
    'reviews'     => array(),
    'reviews_url' => '',

    // Mostrar foto del fundador solo con consentimiento del responsable legal.
    'founder_photo_consent' => false,

    // Google Maps (iframe) del área general, sin pin de negocio ni dirección privada.
    // !6i10 = zoom; !5m2!1sen!2sus = mapa en inglés. Vacío = no se muestra el mapa.
    'map_embed' => 'https://www.google.com/maps/embed?origin=mfe&pb=!1m3!2m1!1sOrange+County,+California!6i10!5m2!1sen!2sus',

    // ID del contenedor de Google Tag Manager (GTM-XXXXXXX). Vacío = no se carga.
    'gtm_id' => '',

    // Ficha de producto: agregar filas solo cuando el cliente las confirme
    // (tamaño/porciones, sabores, ingredientes y alérgenos, precio, pickup/delivery).
    'product_details' => array(
      array('label' => 'Made to order', 'value' => 'Please allow at least 24 hours for preparation.'),
    ),

    // Fotos por slot. 'id' = adjunto de la Biblioteca de Medios (WordPress genera srcset).
    // Sin 'id', se usa assets/images/{slot}.jpg|webp (fotogramas del video de preparación).
    // El 'alt' debe describir lo que realmente muestra la foto.
    'media' => array(
      // Home
      'home-signature'      => array('id' => 18, 'alt' => 'A slice of Bocanegra cheesecake showing its creamy texture, served with fresh berries'),
      'home-origin-kitchen' => array('alt' => 'Stirring a pot on the stove with a wooden spoon'),
      'home-origin-hands'   => array('alt' => 'Pouring the cheesecake filling from a blender'),
      'heritage-1'          => array('alt' => 'Hands rolling out the crust with a wooden rolling pin'),
      'heritage-2'          => array('alt' => 'Sugar being poured into a pot on the stove'),
      'heritage-3'          => array('alt' => 'Placing cheesecakes into the oven'),
      'home-cheesecake'     => array('id' => 19, 'alt' => 'A whole Bocanegra cheesecake on a white plate on a garden table'),
      'home-moment'         => array('id' => 15, 'alt' => 'A whole Bocanegra cheesecake set among rose petals with “Yours” and “Mine” teacups'),
      'home-request'        => array('id' => 17, 'alt' => 'A whole Bocanegra cheesecake in its tin beside a leather notebook and pen'),
      // Our Story
      'story-hero'          => array('id' => 16, 'alt' => 'Overhead view of a Bocanegra cheesecake surrounded by yellow roses and a wooden heart'),
      'story-origin'        => array('alt' => 'Hands pressing the crust into a baking tin'),
      'story-new-chapter'   => array('alt' => 'Cheesecake filling being poured into a tin'),
      'story-purpose'       => array('id' => 22, 'alt' => 'A Bocanegra cheesecake on an outdoor table with “Mine” and “Yours” teacups and a notebook'),
      'story-closing'       => array('id' => 18, 'alt' => 'A slice of Bocanegra cheesecake served with fresh berries'),
      // Our Cheesecake
      'cheesecake-hero'     => array('id' => 20, 'alt' => 'Close-up of a whole Bocanegra cheesecake topped with blueberries and mint'),
      'cheesecake-detail'   => array('id' => 21, 'alt' => 'A Bocanegra cheesecake with blueberries on a marble board, next to a cup of coffee'),
      'gallery-1'           => array('id' => 19, 'alt' => 'A whole Bocanegra cheesecake on a white plate'),
      'gallery-2'           => array('id' => 18, 'alt' => 'A slice of Bocanegra cheesecake showing its texture'),
      'gallery-3'           => array('id' => 20, 'alt' => 'Close-up of a Bocanegra cheesecake with blueberries'),
      'gallery-4'           => array('id' => 17, 'alt' => 'A Bocanegra cheesecake in its baking tin'),
      'gallery-5'           => array('alt' => 'Pouring the filling into a tin while preparing a Bocanegra cheesecake'),
      'gallery-6'           => array('id' => 15, 'alt' => 'A Bocanegra cheesecake served at the table with teacups and rose petals'),
      // Request an Order
      'request-hero'        => array('id' => 22, 'alt' => 'A Bocanegra cheesecake on an outdoor table, ready to be shared'),
    ),

    // Slots de la galería de Our Cheesecake (en orden).
    'gallery' => array('gallery-1', 'gallery-2', 'gallery-3', 'gallery-4', 'gallery-5', 'gallery-6'),

    'seo' => array(
      'home' => array(
        'title'       => 'Bocanegra Pastry Boutique | Cheesecakes in Orange County',
        'description' => 'Discover Bocanegra, a family-recipe cheesecake boutique based in Orange County. Made to order with at least 24 hours’ notice.',
      ),
      'story' => array(
        'title'       => 'Our Story | Bocanegra Pastry Boutique',
        'description' => 'From a family recipe in Ensenada to a new generation in Orange County. Discover the story behind Bocanegra cheesecake.',
      ),
      'cheesecake' => array(
        'title'       => 'Our Signature Cheesecake | Bocanegra Pastry Boutique',
        'description' => 'Explore the signature Bocanegra cheesecake, made to order from a treasured family recipe. Request yours in Orange County.',
      ),
      'request' => array(
        'title'       => 'Request a Cheesecake | Bocanegra Pastry Boutique',
        'description' => 'Request a Bocanegra cheesecake in Orange County. Allow at least 24 hours’ notice; all orders are subject to confirmation.',
      ),
    ),
  ));

  return $config;
}

/**
 * Páginas principales (rutas definidas en el brief).
 */
function bn_pages() {
  return array(
    'home'       => array('title' => 'Home', 'slug' => 'home', 'nav' => 'Home'),
    'story'      => array('title' => 'Our Story', 'slug' => 'our-story', 'nav' => 'Our Story'),
    'cheesecake' => array('title' => 'Our Cheesecake', 'slug' => 'our-cheesecake', 'nav' => 'Our Cheesecake'),
    'request'    => array('title' => 'Request an Order', 'slug' => 'request-an-order', 'nav' => 'Request an Order'),
  );
}

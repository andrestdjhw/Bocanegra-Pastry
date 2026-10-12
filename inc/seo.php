<?php
/**
 * SEO básico del tema: títulos, meta descripciones, Open Graph, structured data y sitemap.
 * Si más adelante se instala Yoast o Rank Math, este módulo se desactiva solo.
 */

function bn_seo_plugin_active() {
  return defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION');
}

function bn_seo_meta() {
  $key = bn_current_page_key();
  $seo = bn_config()['seo'];
  return ($key && isset($seo[$key])) ? $seo[$key] : null;
}

function bn_document_title($title) {
  if (bn_seo_plugin_active()) {
    return $title;
  }

  $meta = bn_seo_meta();
  return $meta ? $meta['title'] : $title;
}

add_filter('pre_get_document_title', 'bn_document_title');

function bn_document_title_separator() {
  return '|';
}

add_filter('document_title_separator', 'bn_document_title_separator');

function bn_head_meta() {
  if (bn_seo_plugin_active()) {
    return;
  }

  $config = bn_config();
  $meta   = bn_seo_meta();
  $title  = $meta ? $meta['title'] : wp_get_document_title();
  $desc   = $meta ? $meta['description'] : '';
  $url    = is_front_page() ? home_url('/') : (is_singular() ? get_permalink() : '');
  $og     = bn_find_image('og-image');

  if ($desc) {
    printf("<meta name=\"description\" content=\"%s\">\n", esc_attr($desc));
  }

  printf("<meta property=\"og:site_name\" content=\"%s\">\n", esc_attr($config['brand']));
  printf("<meta property=\"og:type\" content=\"website\">\n");
  printf("<meta property=\"og:title\" content=\"%s\">\n", esc_attr($title));

  if ($desc) {
    printf("<meta property=\"og:description\" content=\"%s\">\n", esc_attr($desc));
  }

  if ($url) {
    printf("<meta property=\"og:url\" content=\"%s\">\n", esc_url($url));
  }

  if ($og) {
    printf("<meta property=\"og:image\" content=\"%s\">\n", esc_url(get_theme_file_uri($og['base'])));
  }

  printf("<meta name=\"twitter:card\" content=\"%s\">\n", $og ? 'summary_large_image' : 'summary');

  if (is_front_page()) {
    bn_print_schema();
  }
}

add_action('wp_head', 'bn_head_meta', 2);

/**
 * Organization + WebSite. Sin dirección, precio, ratings ni disponibilidad
 * (no confirmados). Agregar LocalBusiness/Product solo con datos verificados.
 */
function bn_print_schema() {
  $config = bn_config();
  $org_id = home_url('/#organization');

  $area = array(array('@type' => 'AdministrativeArea', 'name' => 'Orange County, California'));
  foreach ($config['focus'] as $city) {
    $area[] = array('@type' => 'City', 'name' => $city . ', California');
  }

  $org = array(
    '@type'       => 'Organization',
    '@id'         => $org_id,
    'name'        => $config['brand'],
    'url'         => home_url('/'),
    'slogan'      => $config['slogan'],
    'description' => 'A family-recipe cheesecake boutique based in Orange County, California. Every cheesecake is made to order with at least 24 hours’ notice.',
    'areaServed'  => $area,
  );

  if ($logo = bn_logo_url()) {
    $org['logo'] = $logo;
  }
  if ($config['email']) {
    $org['email'] = $config['email'];
  }
  if ($config['phone']) {
    $org['telephone'] = $config['phone'];
  }
  if (array_filter($config['social'])) {
    $org['sameAs'] = array_values(array_filter($config['social']));
  }

  $graph = array(
    '@context' => 'https://schema.org',
    '@graph'   => array(
      $org,
      array(
        '@type'     => 'WebSite',
        'url'       => home_url('/'),
        'name'      => $config['brand'],
        'publisher' => array('@id' => $org_id),
      ),
    ),
  );

  echo '<script type="application/ld+json">' . wp_json_encode($graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</script>\n";
}

// Sitemap nativo de WordPress (/wp-sitemap.xml): solo páginas.
add_filter('wp_sitemaps_add_provider', function ($provider, $name) {
  return $name === 'users' ? false : $provider;
}, 10, 2);

add_filter('wp_sitemaps_taxonomies', '__return_empty_array');

add_filter('wp_sitemaps_post_types', function ($post_types) {
  unset($post_types['post']);
  return $post_types;
});

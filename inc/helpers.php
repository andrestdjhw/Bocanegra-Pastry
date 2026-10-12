<?php
/**
 * Helpers de plantilla.
 */

/**
 * URL de una de las páginas principales (home, story, cheesecake, request).
 */
function bn_url($key) {
  static $cache = array();

  if (isset($cache[$key])) {
    return $cache[$key];
  }

  $pages = bn_pages();

  if ($key === 'home' || !isset($pages[$key])) {
    return $cache[$key] = home_url('/');
  }

  $page = get_page_by_path($pages[$key]['slug']);
  $cache[$key] = $page ? get_permalink($page) : home_url('/' . $pages[$key]['slug'] . '/');

  return $cache[$key];
}

/**
 * Clave de la página actual según bn_pages(), o '' si no es una de ellas.
 */
function bn_current_page_key() {
  if (is_front_page()) {
    return 'home';
  }

  foreach (bn_pages() as $key => $page) {
    if ($key !== 'home' && is_page($page['slug'])) {
      return $key;
    }
  }

  return '';
}

/**
 * Busca una imagen del tema en assets/images/{slot}.{ext}.
 * Devuelve array('base' => ruta relativa, 'avif' => ..., 'webp' => ...) o null.
 */
function bn_find_image($slot) {
  $dir   = get_theme_file_path('assets/images/');
  $found = array();

  foreach (array('avif', 'webp', 'jpg', 'jpeg', 'png', 'svg') as $ext) {
    if (file_exists($dir . $slot . '.' . $ext)) {
      $found[$ext] = 'assets/images/' . $slot . '.' . $ext;
    }
  }

  if (!$found) {
    return null;
  }

  foreach (array('jpg', 'jpeg', 'png', 'webp', 'svg', 'avif') as $ext) {
    if (isset($found[$ext])) {
      $found['base'] = $found[$ext];
      break;
    }
  }

  return $found;
}

/**
 * Foto asignada a un slot en bn_config()['media']: array('id' => adjunto, 'alt' => '...').
 */
function bn_media_config($slot) {
  $media = bn_config()['media'];
  return isset($media[$slot]) ? $media[$slot] : array();
}

/**
 * ¿El slot tiene una foto real (Biblioteca de Medios o assets/images)?
 */
function bn_has_media($slot) {
  $config = bn_media_config($slot);
  return (!empty($config['id']) && wp_attachment_is_image($config['id'])) || bn_find_image($slot) !== null;
}

/**
 * Imprime la foto de un slot. Orden de búsqueda:
 * 1. Adjunto de la Biblioteca de Medios (bn_config()['media'][slot]['id']) — con srcset responsivo.
 * 2. Archivo del tema assets/images/{slot}.jpg (+ opcional .webp / .avif).
 * 3. Bloque decorativo sin texto, para mantener el layout mientras llega la foto.
 * El alt de la configuración (si existe) tiene prioridad sobre el de la plantilla.
 */
function bn_media($slot, $alt, $args = array()) {
  $args = wp_parse_args($args, array(
    'class' => '',
    'eager' => false,
    'sizes' => '(min-width: 1024px) 50vw, 100vw',
    'tone'  => 'light', // 'dark' = placeholder oscuro para secciones espresso
    'reveal' => '',     // animación al hacer scroll: 'zoom' | 'fade' | 'left' | 'right' (ver src/index.css)
  ));

  $config  = bn_media_config($slot);
  $alt     = !empty($config['alt']) ? $config['alt'] : $alt;
  $wrapper = trim('bn-photo ' . $args['class']);
  $reveal  = $args['reveal'] ? sprintf(' data-reveal="%s"', esc_attr($args['reveal'])) : '';
  $loading = $args['eager'] ? array('loading' => 'eager', 'fetchpriority' => 'high') : array('loading' => 'lazy');

  if (!empty($config['id']) && wp_attachment_is_image($config['id'])) {
    echo '<div class="' . esc_attr($wrapper) . '"' . $reveal . '>';
    echo wp_get_attachment_image($config['id'], 'full', false, array_merge(array(
      'alt'      => $alt,
      'class'    => 'h-full w-full object-cover',
      'sizes'    => $args['sizes'],
      'decoding' => 'async',
    ), $loading));
    echo '</div>';
    return;
  }

  $image = bn_find_image($slot);

  if (!$image) {
    $placeholder = $args['tone'] === 'dark' ? 'bn-photo-placeholder bn-photo-placeholder-dark' : 'bn-photo-placeholder';
    printf('<div class="%s %s" aria-hidden="true" data-photo-slot="%s"%s></div>', esc_attr($wrapper), $placeholder, esc_attr($slot), $reveal);
    return;
  }

  $size = @getimagesize(get_theme_file_path($image['base']));

  echo '<div class="' . esc_attr($wrapper) . '"' . $reveal . '><picture>';

  foreach (array('avif', 'webp') as $ext) {
    if (isset($image[$ext]) && $image[$ext] !== $image['base']) {
      printf('<source type="image/%s" srcset="%s">', esc_attr($ext), esc_url(get_theme_file_uri($image[$ext])));
    }
  }

  printf(
    '<img src="%s" alt="%s"%s class="h-full w-full object-cover" decoding="async" %s>',
    esc_url(get_theme_file_uri($image['base'])),
    esc_attr($alt),
    $size ? sprintf(' width="%d" height="%d"', $size[0], $size[1]) : '',
    $args['eager'] ? 'loading="eager" fetchpriority="high"' : 'loading="lazy"'
  );

  echo '</picture></div>';
}

/**
 * Video de fondo (silenciado, en bucle) con poster. Se pausa con prefers-reduced-motion
 * y tiene botón de pausa (src/scripts/videos.js). Sin video, cae a bn_media() con el slot del poster.
 *
 * Video esperado: assets/video/{slot}.mp4 y poster assets/images/{slot}-poster.jpg|webp.
 */
function bn_video($slot, $alt, $args = array()) {
  $file = 'assets/video/' . $slot . '.mp4';

  if (!file_exists(get_theme_file_path($file))) {
    bn_media($slot . '-poster', $alt, $args);
    return;
  }

  $args   = wp_parse_args($args, array('class' => ''));
  $poster = bn_find_image($slot . '-poster');
  $poster = $poster ? get_theme_file_uri(isset($poster['webp']) ? $poster['webp'] : $poster['base']) : '';
  ?>
  <div class="<?php echo esc_attr(trim('bn-photo ' . $args['class'])); ?>">
    <video class="h-full w-full object-cover" autoplay muted loop playsinline preload="auto" data-bg-video aria-label="<?php echo esc_attr($alt); ?>"<?php echo $poster ? ' poster="' . esc_url($poster) . '"' : ''; ?>>
      <source src="<?php echo esc_url(get_theme_file_uri($file)); ?>" type="video/mp4">
    </video>
    <button type="button" hidden data-bg-video-toggle aria-label="Pause background video" class="absolute bottom-4 right-4 z-10 grid h-11 w-11 place-items-center rounded-full bg-espresso/60 text-cream backdrop-blur transition hover:bg-espresso/80">
      <svg aria-hidden="true" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4" data-icon="pause"><path d="M7 5h3.5v14H7zM13.5 5H17v14h-3.5z"/></svg>
      <svg aria-hidden="true" viewBox="0 0 24 24" fill="currentColor" class="hidden h-4 w-4" data-icon="play"><path d="M8 5.5v13l10.5-6.5z"/></svg>
    </button>
  </div>
  <?php
}

/**
 * Logos oficiales (Biblioteca de Medios IDs 23–28), recortados y entintados en colores de marca
 * en assets/images/brand/ (solo cambia el color de relleno; la forma es la del archivo oficial).
 */
function bn_brand_url($name, $ext = 'webp') {
  $file = 'assets/images/brand/' . $name . '.' . $ext;
  return file_exists(get_theme_file_path($file)) ? get_theme_file_uri($file) : '';
}

/**
 * <picture> de un logo (WebP + PNG de respaldo). $alt vacío = decorativo.
 */
function bn_brand_img($name, $alt = '', $class = '', $attrs = '') {
  $png = bn_brand_url($name, 'png');
  if (!$png) {
    return '';
  }

  $size = @getimagesize(get_theme_file_path('assets/images/brand/' . $name . '.png'));

  return sprintf(
    '<picture><source type="image/webp" srcset="%s"><img src="%s" alt="%s" class="%s"%s %s></picture>',
    esc_url(bn_brand_url($name)),
    esc_url($png),
    esc_attr($alt),
    esc_attr($class),
    $size ? sprintf(' width="%d" height="%d"', $size[0], $size[1]) : '',
    $attrs
  );
}

/**
 * Sello de marca superpuesto a la esquina de una foto (piezas de apoyo, colores oficiales).
 * Es decorativo: el nombre de la marca ya está en la página. $position = clases de posición/tamaño/rotación.
 */
function bn_stamp($name, $position, $disc = false) {
  // $disc: fondo circular crema para sellos sin fondo propio, que sobre una foto no se leerían.
  $img = $disc
    ? '<div class="rounded-full bg-cream p-[7%] shadow-[0_12px_24px_rgb(34_22_14/0.28)]">' . bn_brand_img($name, '', 'h-auto w-full', 'loading="lazy"') . '</div>'
    : bn_brand_img($name, '', 'h-auto w-full drop-shadow-[0_12px_24px_rgb(34_22_14/0.28)]', 'loading="lazy"');

  printf(
    '<div aria-hidden="true" data-reveal="zoom" class="pointer-events-none absolute z-10 %s">%s</div>',
    esc_attr($position),
    $img
  );
}

/**
 * Logo para structured data (PNG, fondo transparente).
 */
function bn_logo_url() {
  return bn_brand_url('isologo-chocolate', 'png');
}

function bn_arrow() {
  return '<svg aria-hidden="true" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" class="h-4 w-4 shrink-0"><path d="M4 10h12M11 5l5 5-5 5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
}

/**
 * Íconos de línea simples para bandas de microconceptos.
 */
function bn_icon($name) {
  $paths = array(
    'recipe'  => '<path d="M6 4h9l3 3v13H6z"/><path d="M15 4v3h3M9 11h6M9 15h6"/>',
    'clock'   => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
    'chat'    => '<path d="M4.5 6.5h15v9h-8l-4 3.5v-3.5h-3z"/>',
    'heritage'=> '<path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10z"/>',
    'wheat'   => '<path d="M12 21V8"/><path d="M12 12c-2.5 0-4-1.5-4-4 2.5 0 4 1.5 4 4zM12 12c2.5 0 4-1.5 4-4-2.5 0-4 1.5-4 4zM12 16c-2.5 0-4-1.5-4-4 2.5 0 4 1.5 4 4zM12 16c2.5 0 4-1.5 4-4-2.5 0-4 1.5-4 4zM12 8c-1.6-.8-2-2.4-1.2-4 1.6.8 2 2.4 1.2 4z"/>',
    'hat'     => '<path d="M7 14.5a4 4 0 0 1 .6-7.9 4.5 4.5 0 0 1 8.8 0 4 4 0 0 1 .6 7.9V19H7z"/><path d="M7 16.5h10"/>',
  );

  if (!isset($paths[$name])) {
    return '';
  }

  return '<svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-7 w-7">' . $paths[$name] . '</svg>';
}

/**
 * Ilustración de una rebanada (módulo "tres generaciones").
 */
function bn_slice_icon() {
  return '<svg aria-hidden="true" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" class="h-14 w-14"><path d="M8 30 56 18v16z"/><path d="M8 30 56 34v14L8 42z"/><path d="M8 38l48 5" stroke-dasharray="2 3"/></svg>';
}

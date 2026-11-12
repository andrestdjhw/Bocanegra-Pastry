<?php
/**
 * Respaldos de UpdraftPlus: excluir carpetas de desarrollo de los temas.
 *
 * node_modules solo se usa para compilar (npm run build); el sitio funciona con build/.
 * Pesa ~600 MB y ~60,000 archivos, así que excluirlo hace los respaldos mucho más rápidos y ligeros.
 * Para restaurar el entorno de desarrollo basta con `npm install`.
 *
 * Solo aplica dentro de wp-content/themes/, para no afectar a plugins.
 */
function bn_updraft_exclude_dev_dirs($exclude, $fullpath) {
  if ($exclude) {
    return $exclude;
  }

  $themes_root = trailingslashit(wp_normalize_path(get_theme_root()));
  $path        = wp_normalize_path($fullpath);

  if (strpos($path, $themes_root) !== 0) {
    return false;
  }

  return in_array(basename($path), array('node_modules'), true);
}

add_filter('updraftplus_exclude_directory', 'bn_updraft_exclude_dev_dirs', 10, 2);

<?php
/**
 * Composición de stickers de marca (como la laptop del look & feel) para zonas que quedan vacías.
 * $args['items']: array(array('pieza', 'clases de tamaño/rotación/solape')), $args['class']: clases del contenedor.
 * Decorativo: aria-hidden. Aparecen en cascada (data-reveal-stagger) y se elevan al pasar el mouse.
 */
$items = $args['items'] ?? array();
?>
<div aria-hidden="true" data-reveal-stagger class="flex flex-wrap items-center <?php echo esc_attr($args['class'] ?? ''); ?>">
  <?php foreach ($items as $item) : ?>
    <span class="block shrink-0 <?php echo esc_attr($item[1]); ?>">
      <?php echo bn_brand_img($item[0], '', 'bn-sticker h-auto w-full', 'loading="lazy"'); ?>
    </span>
  <?php endforeach; ?>
</div>

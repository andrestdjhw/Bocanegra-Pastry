<?php
/**
 * Versión server-side de la Topbar (sin JS / buscadores). React la reemplaza al montar.
 */
$config = bn_config();
?>
<div class="bg-espresso text-xs text-cream/85">
  <div class="bn-container flex min-h-10 items-center gap-5 py-2">
    <?php if ($config['phone']) : ?>
      <a href="tel:<?php echo esc_attr($config['phone']); ?>"><?php echo esc_html($config['phone_display'] ?: $config['phone']); ?></a>
    <?php endif; ?>
    <?php if ($config['email']) : ?>
      <a href="mailto:<?php echo esc_attr($config['email']); ?>"><?php echo esc_html($config['email']); ?></a>
    <?php endif; ?>
    <?php if (!$config['phone'] && !$config['email']) : ?>
      <span><?php echo esc_html($config['area']); ?></span>
    <?php endif; ?>
  </div>
</div>

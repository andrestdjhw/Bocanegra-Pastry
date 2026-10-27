<?php
/**
 * Versión server-side del Footer (sin JS / buscadores). React la reemplaza al montar.
 */
$config = bn_config();
?>
<footer class="bn-scallop-top bg-espresso text-cream">
  <div class="bn-container py-16">
    <?php echo bn_brand_img('isologo-cream', $config['brand'], 'h-24 w-auto', 'loading="lazy"'); ?>
    <p class="mt-3 text-cream/80"><?php echo esc_html($config['slogan']); ?> Based in <?php echo esc_html($config['area']); ?>.</p>
    <ul class="mt-8 flex flex-wrap gap-x-8 gap-y-3 text-sm">
      <?php foreach (bn_pages() as $key => $page) : ?>
        <li><a href="<?php echo esc_url(bn_url($key)); ?>"><?php echo esc_html($page['nav']); ?></a></li>
      <?php endforeach; ?>
      <?php if (get_privacy_policy_url()) : ?>
        <li><a href="<?php echo esc_url(get_privacy_policy_url()); ?>">Privacy Policy</a></li>
      <?php endif; ?>
      <?php if (bn_terms_url()) : ?>
        <li><a href="<?php echo esc_url(bn_terms_url()); ?>">Terms &amp; Conditions</a></li>
      <?php endif; ?>
    </ul>
  </div>
</footer>

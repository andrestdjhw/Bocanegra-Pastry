<?php
/**
 * Versión server-side del Navbar: se ve antes de que cargue React,
 * sin JavaScript y la leen los buscadores. React la reemplaza al montar.
 */
$current = bn_current_page_key();
?>
<header class="border-b border-transparent bg-cream">
  <nav aria-label="Main" class="bn-container flex h-18 items-center justify-between gap-4">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="flex flex-col leading-none">
      <?php echo bn_brand_img('logo-script-chocolate', bn_config()['brand'], 'h-9 w-auto sm:h-11 lg:h-13', 'fetchpriority="high"'); ?>
    </a>
    <ul class="hidden items-center gap-8 lg:flex">
      <?php foreach (bn_pages() as $key => $page) : ?>
        <li>
          <a href="<?php echo esc_url(bn_url($key)); ?>" class="text-sm"<?php echo $current === $key ? ' aria-current="page"' : ''; ?>><?php echo esc_html($page['nav']); ?></a>
        </li>
      <?php endforeach; ?>
    </ul>
    <a href="<?php echo esc_url(bn_url('request')); ?>" class="bn-btn bn-btn-primary min-h-10 px-4 py-2 text-[0.65rem]">Request a Cheesecake</a>
  </nav>
</header>

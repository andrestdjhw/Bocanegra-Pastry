<?php

get_header(); ?>

<section class="bn-container py-24 text-center md:py-32">
  <?php echo bn_brand_img('rebanada-lisa', '', 'mx-auto mb-8 h-auto w-40', 'aria-hidden="true"'); ?>
  <p class="bn-eyebrow justify-center">Page Not Found</p>
  <h1 class="bn-h1 mt-6">This Slice Is Missing.</h1>
  <p class="bn-lead mx-auto mt-6 max-w-xl">The page you’re looking for doesn’t exist or has moved.</p>
  <div class="mt-9 flex flex-col justify-center gap-3 sm:flex-row">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="bn-btn bn-btn-outline">Back to Home</a>
    <a href="<?php echo esc_url(bn_url('request')); ?>" class="bn-btn bn-btn-primary">Request a Cheesecake <?php echo bn_arrow(); ?></a>
  </div>
</section>

<?php get_footer();

<?php
/**
 * Our Story — /our-story/
 */
get_header();

$config = bn_config();
?>

<!-- 01 | Story hero -->
<section>
  <div class="bn-container grid items-center gap-10 pb-16 pt-8 md:pt-12 lg:grid-cols-[55fr_45fr] lg:gap-16 lg:pb-24 lg:pt-16">
    <div class="bn-hero-in max-w-2xl">
      <p class="bn-eyebrow">Our Beginning</p>
      <h1 class="bn-h1 mt-6">A Family Recipe With a Place to Call Home.</h1>
      <p class="bn-lead mt-6 max-w-xl">The name Bocanegra comes from a street in Ensenada, Mexico, where a family cheesecake recipe became part of something worth passing on.</p>
      <a href="<?php echo esc_url(bn_url('request')); ?>" class="bn-btn bn-btn-primary mt-9">Request Your Cheesecake <?php echo bn_arrow(); ?></a>
    </div>
    <div class="relative">
      <?php bn_media('story-hero', 'A warm, editorial view of the Bocanegra family recipe', array('class' => 'bn-hero-media aspect-[4/3] lg:aspect-[4/5]', 'eager' => true)); ?>
      <?php bn_stamp('sello-ensenada', '-bottom-10 -left-2 w-32 -rotate-6 sm:w-40 lg:-left-14 lg:w-48', true); ?>
    </div>
  </div>
</section>

<!-- 02 | The recipe and its origin -->
<section class="bn-section bg-cream-200">
  <div class="bn-container grid items-center gap-10 lg:grid-cols-[60fr_40fr] lg:gap-16">
    <?php bn_media('story-origin', 'The family kitchen where the Bocanegra cheesecake recipe began', array('class' => 'aspect-[4/3]', 'reveal' => 'zoom')); ?>
    <div data-reveal-stagger>
      <?php echo bn_brand_img('banner-ensenada', 'Bocanegra Pastry Boutique — Ensenada, México', 'h-auto w-56 sm:w-64', 'loading="lazy"'); ?>
      <h2 class="bn-h2 mt-5">Where Our Story Began.</h2>
      <p class="bn-body mt-6">Years ago, a grandfather perfected a cheesecake recipe and shared the experience of making it with his family. Those moments in Ensenada became part of the story behind Bocanegra.</p>
    </div>
  </div>
</section>

<!-- 03 | A new generation -->
<section class="bn-scallop-top bn-section bg-espresso text-cream [--bn-scallop:var(--color-cream-200)]">
  <div class="bn-container grid items-center gap-10 lg:grid-cols-[40fr_60fr] lg:gap-20">
    <div data-reveal="right" class="lg:order-2">
      <?php
      // Foto real del fundador solo con consentimiento del responsable legal.
      if ($config['founder_photo_consent']) {
        bn_media('story-founder', 'The young founder of Bocanegra Pastry Boutique', array('class' => 'aspect-[4/3]'));
      } else {
        bn_media('story-new-chapter', 'Hands carrying the family recipe forward', array('class' => 'aspect-[4/3]'));
      }
      ?>
    </div>
    <div data-reveal-stagger class="lg:order-1">
      <p class="bn-eyebrow bn-eyebrow-light">A New Generation</p>
      <h2 class="bn-h2 mt-5">A New Chapter, Written at Twelve.</h2>
      <p class="mt-6 text-lg leading-relaxed text-cream/80">Today, his 12-year-old grandson is continuing the recipe as the founder of Bocanegra Pastry Boutique. It is a beginning shaped by family, dedication, and the belief that something meaningful can be carried forward.</p>
    </div>
  </div>
</section>

<!-- 04 | Our purpose -->
<section class="bn-section">
  <div class="bn-container">
    <div class="relative">
      <?php bn_media('story-purpose', 'A Bocanegra cheesecake shared around a table', array('class' => 'aspect-[4/3] md:aspect-[21/9]', 'reveal' => 'zoom')); ?>
      <div data-reveal-stagger class="relative -mt-16 mx-4 bg-cream p-8 shadow-sm ring-1 ring-chocolate/10 sm:mx-8 md:absolute md:bottom-8 md:left-8 md:mx-0 md:mt-0 md:max-w-lg md:p-10">
        <p class="bn-eyebrow">Our Purpose</p>
        <h2 class="mt-4 font-display text-4xl font-medium leading-none sm:text-5xl">More Than a Cheesecake.</h2>
        <p class="bn-body mt-5">We hope every slice brings people together—and reminds the next generation that meaningful things can begin at any age.</p>
        <a href="<?php echo esc_url(bn_url('cheesecake')); ?>" class="bn-link mt-7">Discover Our Cheesecake <?php echo bn_arrow(); ?></a>
      </div>
    </div>
  </div>
</section>

<!-- 05 | Closing CTA -->
<section class="pb-20 md:pb-28">
  <div class="bn-container">
    <div data-reveal="zoom" class="grid items-center overflow-hidden bg-butter md:grid-cols-[45fr_55fr]">
      <?php bn_media('story-closing', 'A slice of Bocanegra cheesecake', array('class' => 'aspect-[4/3] rounded-none md:aspect-auto md:h-full md:min-h-[24rem]')); ?>
      <div data-reveal-stagger class="p-8 sm:p-12 lg:p-16">
        <?php echo bn_brand_img('etiqueta-slogan', '', 'mb-8 h-auto w-48 -rotate-3 sm:w-56', 'aria-hidden="true" loading="lazy"'); ?>
        <h2 class="bn-h2">The Story Continues With Every Slice.</h2>
        <p class="bn-body mt-6">Discover the cheesecake inspired by our family’s recipe and request yours for an upcoming moment.</p>
        <a href="<?php echo esc_url(bn_url('request')); ?>" class="bn-btn bn-btn-primary mt-9">Request Your Cheesecake <?php echo bn_arrow(); ?></a>
      </div>
    </div>
  </div>
</section>

<?php get_footer();

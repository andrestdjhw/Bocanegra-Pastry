<?php
/**
 * Our Cheesecake — /our-cheesecake/
 */
get_header();

$config = bn_config();

// Galería: solo fotos reales; mientras no haya, se muestran 4 bloques decorativos.
$gallery = array_values(array_filter($config['gallery'], 'bn_has_media'));

if (!$gallery) {
  $gallery = array_slice($config['gallery'], 0, 4);
}
?>

<!-- 01 | Product hero -->
<section>
  <div class="bn-container grid items-center gap-10 pb-16 pt-8 md:pt-12 lg:grid-cols-[45fr_55fr] lg:gap-16 lg:pb-24 lg:pt-16">
    <div class="bn-hero-in max-w-xl">
      <p class="bn-eyebrow">The Bocanegra Cheesecake</p>
      <h1 class="bn-h1 mt-6">Our Signature Family Cheesecake.</h1>
      <p class="bn-lead mt-6">Crafted from a recipe with roots in Ensenada, Mexico, and prepared to order for the moments that matter.</p>
      <a href="<?php echo esc_url(bn_url('request')); ?>" class="bn-btn bn-btn-primary mt-9">Request a Cheesecake <?php echo bn_arrow(); ?></a>
    </div>
    <div class="relative">
      <?php bn_media('cheesecake-hero', 'The signature Bocanegra cheesecake, whole and sliced', array('class' => 'bn-hero-media aspect-[4/3] lg:aspect-[5/4]', 'eager' => true)); ?>
      <?php bn_stamp('sello-circular', '-bottom-8 -right-2 w-24 rotate-6 sm:w-32 lg:-right-10 lg:w-40'); ?>
    </div>
  </div>
</section>

<!-- 02 | What makes it different -->
<section class="bn-scallop-top bn-section relative overflow-clip bg-espresso text-cream [--bn-scallop:var(--color-cream)]">
  <?php echo bn_brand_img('isologo-cream', '', 'pointer-events-none absolute -bottom-32 -right-24 w-[34rem] max-w-none opacity-[0.06]', 'aria-hidden="true" loading="lazy"'); ?>
  <div class="bn-container relative">
    <div data-reveal-stagger class="max-w-3xl">
      <p class="bn-eyebrow bn-eyebrow-light">What Makes It Different</p>
      <h2 class="bn-h2 mt-5">A Cheesecake With a Story Behind It.</h2>
      <p class="mt-6 text-lg leading-relaxed text-cream/80">Our recipe connects the family kitchen where it began to the next generation carrying it forward. It is the heart of Bocanegra—and the reason we began.</p>
    </div>
    <ul data-reveal-stagger class="mt-14 grid gap-5 md:grid-cols-3">
      <?php
      $pillars = array(
        array('icon' => 'recipe', 'title' => 'Family recipe', 'text' => 'Perfected by a grandfather in Ensenada, Mexico.'),
        array('icon' => 'heritage', 'title' => 'Generational heritage', 'text' => 'Shared across generations and carried forward by his grandson.'),
        array('icon' => 'clock', 'title' => 'Made to order', 'text' => 'Prepared upon request. Please allow at least 24 hours.'),
      );
      foreach ($pillars as $pillar) :
        ?>
        <li data-tilt class="border border-cream/15 bg-espresso p-8">
          <span data-tilt-depth class="inline-block text-butter"><?php echo bn_icon($pillar['icon']); ?></span>
          <h3 data-tilt-depth class="mt-6 font-display text-3xl font-medium"><?php echo esc_html($pillar['title']); ?></h3>
          <p class="mt-3 leading-relaxed text-cream/75"><?php echo esc_html($pillar['text']); ?></p>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<!-- 03 | Product details (solo filas confirmadas, ver inc/config.php) -->
<section class="bn-section">
  <div class="bn-container grid items-center gap-10 lg:grid-cols-[40fr_60fr] lg:gap-20">
    <?php bn_media('cheesecake-detail', 'A Bocanegra cheesecake slice showing its layers', array('class' => 'aspect-[4/5]', 'reveal' => 'zoom')); ?>
    <div data-reveal-stagger>
      <?php echo bn_brand_img('rebanada-lisa', '', 'mb-6 h-auto w-28', 'aria-hidden="true" loading="lazy"'); ?>
      <p class="bn-eyebrow">Product Details</p>
      <h2 class="bn-h2 mt-5">Everything You Need to Know.</h2>
      <dl data-reveal-stagger class="mt-10 divide-y divide-chocolate/15 border-y border-chocolate/15">
        <?php foreach ($config['product_details'] as $row) : ?>
          <div class="grid gap-2 py-6 sm:grid-cols-[14rem_1fr] sm:gap-8">
            <dt class="font-display text-2xl font-medium"><?php echo esc_html($row['label']); ?></dt>
            <dd class="bn-body"><?php echo esc_html($row['value']); ?></dd>
          </div>
        <?php endforeach; ?>
      </dl>
      <?php
      get_template_part('template-parts/sticker-cluster', null, array(
        'class' => 'mt-14 hidden justify-end lg:flex',
        'items' => array(
          array('emblema-marco-chocolate', 'w-56 -rotate-3'),
          array('sello-ovalado', 'ml-4 mt-14 w-40 rotate-6'),
        ),
      ));
      ?>
    </div>
  </div>
</section>

<!-- 04 | Product photo story -->
<section class="bn-section bg-cream-200">
  <div class="bn-container">
    <div data-reveal-stagger class="max-w-2xl">
      <p class="bn-eyebrow">Gallery</p>
      <h2 class="bn-h2 mt-5">A Closer Look at Our Cheesecake.</h2>
      <p class="bn-body mt-6">Explore the details of our signature cheesecake.</p>
    </div>
    <div data-reveal-stagger class="mt-12 grid grid-cols-2 gap-4 md:gap-6 <?php echo count($gallery) > 4 ? 'lg:grid-cols-3' : 'lg:grid-cols-4'; ?>">
      <?php foreach ($gallery as $slot) : ?>
        <div data-tilt>
          <?php bn_media($slot, '', array('class' => 'bn-zoom-hover aspect-square', 'sizes' => '(min-width: 1024px) 33vw, 50vw')); ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- 05 | How requests work -->
<section class="bn-section">
  <div class="bn-container">
    <div data-reveal-stagger class="mx-auto max-w-3xl text-center">
      <p class="bn-eyebrow justify-center">How Requests Work</p>
      <h2 class="bn-h2 mt-5">Made to Order, Made for Your Moment.</h2>
    </div>
    <ol class="relative mt-14 grid gap-10 md:grid-cols-3 md:gap-8">
      <li aria-hidden="true" data-reveal="line" class="absolute left-[16.6%] right-[16.6%] top-7 hidden h-px bg-gold/60 md:block"></li>
      <?php
      $steps = array(
        'Tell us your preferred date and how many cheesecakes you need.',
        'We’ll review your request and confirm the details with you.',
        'Once confirmed, we’ll share the next steps for your order.',
      );
      foreach ($steps as $i => $step) :
        ?>
        <li data-reveal class="relative flex flex-col items-center text-center" style="transition-delay: <?php echo esc_attr($i * 0.15); ?>s">
          <span class="grid h-14 w-14 place-items-center rounded-full bg-chocolate font-display text-2xl font-medium text-cream"><?php echo $i + 1; ?></span>
          <p class="mt-3 text-xs font-bold uppercase tracking-[0.22em] text-gold-700">Step <?php echo $i + 1; ?></p>
          <p class="bn-body mt-3 max-w-xs"><?php echo esc_html($step); ?></p>
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>

<!-- 06 | Product FAQ + conversion -->
<section class="pb-20 md:pb-28">
  <div class="bn-container grid gap-10 lg:grid-cols-[3fr_2fr] lg:gap-16">
    <div>
      <h2 data-reveal class="bn-h2">Before You Request.</h2>
      <div class="mt-10">
        <?php
        get_template_part('template-parts/faq-list', null, array('items' => array(
          array('q' => 'Can I request a cheesecake for tomorrow?', 'a' => 'Please allow at least 24 hours before your preferred date and time. Availability must still be confirmed.'),
          array('q' => 'Can I request more than one cheesecake?', 'a' => 'Yes. Tell us the quantity you need in the request form; we’ll confirm availability.'),
          array('q' => 'How can I find out about size, ingredients, or allergens?', 'a' => 'Include your question with your request and our team can provide confirmed product details.'),
        )));
        ?>
      </div>
    </div>
    <div class="lg:self-start">
      <aside data-reveal="right" class="flex flex-col justify-between bg-espresso p-8 text-cream sm:p-12">
      <p class="font-display text-4xl font-medium leading-none sm:text-5xl">Ready to Share a Slice of Our Story?</p>
      <p class="mt-6 leading-relaxed text-cream/80">Every cheesecake is made to order. Please allow at least 24 hours.</p>
      <a href="<?php echo esc_url(bn_url('request')); ?>" class="bn-btn bn-btn-primary mt-9 self-start">Request Your Cheesecake <?php echo bn_arrow(); ?></a>
    </aside>
      <?php
      get_template_part('template-parts/sticker-cluster', null, array(
        'class' => 'mt-12 hidden justify-center lg:flex',
        'items' => array(
          array('sello-ensenada', 'w-36 -rotate-6'),
          array('emblema-slogan-chocolate', 'ml-4 mt-14 w-52 rotate-3'),
        ),
      ));
      ?>
    </div>
  </div>
</section>

<?php get_footer();

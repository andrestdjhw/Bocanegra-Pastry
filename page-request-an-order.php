<?php
/**
 * Request an Order — /request-an-order/
 * Diseño calmado: sin carrito, sin checkout, sin pagos.
 */
get_header();

$config = bn_config();
?>

<!-- 01 | Request hero + 02 | Order request form -->
<section class="pb-20 pt-8 md:pb-28 md:pt-12 lg:pt-16">
  <div class="bn-container grid items-start gap-10 lg:grid-cols-[5fr_7fr] lg:gap-16">
    <div class="bn-hero-in lg:sticky lg:top-28">
      <p class="bn-eyebrow">Prepared Upon Request</p>
      <h1 class="bn-h1 mt-6">Request Your Bocanegra Cheesecake.</h1>
      <p class="bn-lead mt-6">Tell us a little about your order. Every cheesecake is prepared to order, so please allow at least 24 hours before your preferred date and time.</p>
      <p class="mt-6 flex items-start gap-3 border border-chocolate/15 bg-cream-200 p-4 text-sm leading-relaxed">
        <span class="text-gold-700"><?php echo bn_icon('chat'); ?></span>
        <span><strong>Please note:</strong> Submitting this form does not confirm your order.</span>
      </p>
      <div class="relative mt-10 hidden lg:block">
        <?php bn_media('request-hero', 'A Bocanegra cheesecake ready to be prepared upon request', array('class' => 'aspect-[4/3]', 'eager' => true)); ?>
        <?php bn_stamp('sello-triangular', '-right-8 -top-10 w-32 rotate-6'); ?>
      </div>
    </div>

    <div id="request-form" data-reveal="fade">
      <h2 class="font-display text-4xl font-medium leading-none sm:text-5xl">Let’s Get the Details.</h2>
      <div class="mt-8">
        <?php get_template_part('template-parts/contact-form', null, array('variant' => 'page')); ?>
      </div>
    </div>
  </div>
</section>

<!-- 03 | After you submit -->
<section class="bn-scallop-top bn-section bg-espresso text-cream [--bn-scallop:var(--color-cream)]">
  <div class="bn-container">
    <h2 data-reveal class="bn-h2 max-w-2xl">What Happens Next?</h2>
    <ol data-reveal-stagger class="mt-12 grid gap-5 md:grid-cols-3">
      <?php
      $steps = array(
        'We receive your request.',
        'We contact you to review availability and order details.',
        'Your cheesecake is scheduled only after confirmation.',
      );
      foreach ($steps as $i => $step) :
        ?>
        <li data-tilt class="border border-cream/15 bg-espresso p-8">
          <span data-tilt-depth class="block font-display-lg text-6xl font-bold text-butter"><?php echo $i + 1; ?></span>
          <p class="mt-4 text-lg leading-relaxed text-cream/85"><?php echo esc_html($step); ?></p>
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>

<!-- 04 | Contact details / geographic context -->
<section class="bn-section">
  <div class="bn-container grid items-center gap-10 lg:grid-cols-2 lg:gap-20">
    <div data-reveal-stagger>
      <h2 class="bn-h2">Have a Question?</h2>
      <p class="bn-body mt-6">Based in Orange County, California, with a focus on Irvine and Mission Viejo.</p>
      <?php if ($config['phone'] || $config['email']) : ?>
        <ul class="mt-8 space-y-4">
          <?php if ($config['phone']) : ?>
            <li><a href="tel:<?php echo esc_attr($config['phone']); ?>" class="bn-link">Call <?php echo esc_html($config['phone_display'] ?: $config['phone']); ?></a></li>
          <?php endif; ?>
          <?php if ($config['email']) : ?>
            <li><a href="mailto:<?php echo esc_attr($config['email']); ?>" class="bn-link">Email <?php echo esc_html($config['email']); ?></a></li>
          <?php endif; ?>
        </ul>
      <?php else : ?>
        <p class="bn-body mt-4">Include your question with your request and our team will follow up.</p>
      <?php endif; ?>
      <?php
      get_template_part('template-parts/sticker-cluster', null, array(
        'class' => 'mt-12 hidden lg:flex',
        'items' => array(
          array('etiqueta-slogan', 'w-56 -rotate-3'),
          array('sello-circular', 'ml-4 mt-12 w-32 rotate-6'),
        ),
      ));
      ?>
    </div>
    <?php get_template_part('template-parts/oc-map'); ?>
  </div>
</section>

<!-- Banda fija en móvil: se oculta mientras el formulario está en pantalla (src/scripts/mobileRequestBand.js) -->
<div id="bn-mobile-request-band" hidden class="fixed inset-x-0 bottom-0 z-40 border-t border-chocolate/10 bg-cream/95 px-4 py-3 backdrop-blur transition-transform duration-300 lg:hidden">
  <div class="flex items-center justify-between gap-3">
    <p class="text-xs leading-snug text-cocoa">Made to order · at least 24 hours’ notice</p>
    <a href="#request-form" class="bn-btn bn-btn-primary min-h-10 shrink-0 px-4 py-2 text-xs">Start Request</a>
  </div>
</div>

<?php get_footer();

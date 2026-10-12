<?php
/**
 * Home — /
 * Layout basado en el mockup aprobado; copy del Website Copy & Creative Brief.
 */
get_header();

$config = bn_config();
?>

<!-- 01 | Hero: foto a sangre completa sobre fondo espresso -->
<section class="bn-scallop-top bn-hero-gradient relative overflow-hidden bg-espresso text-cream [--bn-scallop:var(--color-cream)]">
  <div class="relative overflow-hidden lg:absolute lg:inset-y-0 lg:right-0 lg:w-[68%]">
    <?php bn_video('home-hero', 'Preparing a Bocanegra cheesecake, from the crust to the finished cake', array('class' => 'bn-hero-media aspect-[4/3] w-full lg:aspect-auto lg:h-full', 'eager' => true, 'tone' => 'dark')); ?>
    <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-espresso to-transparent lg:hidden"></div>
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 hidden bg-gradient-to-r from-espresso via-espresso/30 to-transparent lg:block"></div>
  </div>
  <div class="bn-container relative z-10 pb-14 pt-2 lg:flex lg:min-h-[40rem] lg:items-center lg:py-24">
    <div class="bn-hero-in max-w-xl">
      <p class="bn-eyebrow bn-eyebrow-light">Handcrafted in Orange County</p>
      <h1 class="bn-h1 mt-5 text-cream">Every Slice Tells a Story.</h1>
      <p class="mt-6 max-w-md text-lg leading-relaxed text-cream/80">A family cheesecake recipe, passed from one generation to the next. Made to order in Orange County, California.</p>
      <div class="mt-9 flex flex-col items-start gap-5 sm:flex-row sm:items-center sm:gap-8">
        <a href="<?php echo esc_url(bn_url('request')); ?>" class="bn-btn bn-btn-primary">Request Your Cheesecake <?php echo bn_arrow(); ?></a>
        <a href="<?php echo esc_url(bn_url('story')); ?>" class="bn-link text-cream">Discover Our Story <?php echo bn_arrow(); ?></a>
      </div>
    </div>
  </div>
</section>

<!-- Banda editorial: origen → hoy -->
<section class="bg-cream py-10 md:py-14">
  <div class="bn-container max-w-4xl text-center">
    <div data-reveal="fade" class="flex items-center gap-4 text-[0.65rem] font-bold uppercase tracking-[0.26em] text-gold-700">
      <span>Ensenada, Mexico</span>
      <span aria-hidden="true" data-reveal="line" class="h-px flex-1 bg-gold/60"></span>
      <?php echo bn_brand_img('isotipo-chocolate', '', 'h-9 w-auto shrink-0 md:h-11', 'aria-hidden="true" loading="lazy"'); ?>
      <span aria-hidden="true" data-reveal="line" class="h-px flex-1 bg-gold/60"></span>
      <span>Orange County, California</span>
    </div>
    <p data-reveal-stagger class="mt-6 font-display text-2xl font-semibold uppercase leading-tight tracking-[0.12em] text-chocolate md:text-3xl"><span class="block">One recipe.</span><span class="block">Three generations.</span><span class="block">A new chapter.</span></p>
  </div>
</section>

<!-- 02 | The signature cheesecake: split 60/40 -->
<section class="bg-cream-200">
  <div class="grid lg:grid-cols-[58fr_42fr]">
    <?php bn_media('home-signature', 'Close-up of a clean slice of Bocanegra cheesecake showing its texture', array('class' => 'aspect-[4/3] lg:aspect-auto lg:h-full lg:min-h-[34rem]', 'sizes' => '(min-width: 1024px) 58vw, 100vw', 'reveal' => 'fade')); ?>
    <div class="bn-split-copy">
      <div data-reveal-stagger class="max-w-md">
        <p class="bn-eyebrow">Our Signature</p>
        <h2 class="bn-h2 mt-5">One Recipe. Generations of Care.</h2>
        <p class="bn-body mt-6">At Bocanegra, the cheesecake is the centerpiece. Our family recipe began in Ensenada, Mexico, and continues today in every order we prepare.</p>
        <p class="mt-5 text-sm italic leading-relaxed text-cocoa">Made to order. Please allow at least 24 hours for preparation.</p>
        <a href="<?php echo esc_url(bn_url('cheesecake')); ?>" class="bn-btn bn-btn-primary mt-9">Explore Our Cheesecake <?php echo bn_arrow(); ?></a>
      </div>
    </div>
  </div>
</section>

<!-- 03 | Origin story teaser -->
<section class="bn-scallop-top relative overflow-hidden bg-espresso text-cream [--bn-scallop:var(--color-cream-200)]">
  <div class="bn-container py-16 md:py-24">
    <p data-reveal="fade" class="bn-eyebrow bn-eyebrow-light justify-center">Ensenada, Baja California</p>
    <div data-parallax="0.12" data-parallax-axis="x" class="mx-auto mt-6 w-full max-w-4xl px-2" data-reveal="fade">
      <?php echo bn_brand_img('logo-arco-butter', '', 'h-auto w-full', 'aria-hidden="true" loading="lazy"'); ?>
    </div>

    <div class="mt-12 grid items-center gap-12 lg:mt-16 lg:grid-cols-2 lg:gap-16">
      <div data-reveal-stagger class="max-w-lg">
        <h2 class="bn-h2 text-cream">It Started on a Street Called Bocanegra.</h2>
        <p class="mt-6 text-lg leading-relaxed text-cream/80">In Ensenada, Mexico, a grandfather perfected a cheesecake recipe that became part of his family’s story. Today, his 12-year-old grandson is giving that recipe a new chapter.</p>
        <a href="<?php echo esc_url(bn_url('story')); ?>" class="bn-btn bn-btn-primary mt-9">Read Our Story <?php echo bn_arrow(); ?></a>
      </div>

      <!-- Fotos tipo instantánea: foto histórica de Ensenada (si está autorizada) + manos preparando el pastel -->
      <div class="relative mx-auto hidden h-[26rem] w-full max-w-xl md:block">
        <div class="absolute left-0 top-0 w-[66%]" data-parallax="-0.08">
        <figure data-reveal="left" class="relative -rotate-3 bg-cream p-3 pb-12 shadow-2xl shadow-black/40">
          <?php if (bn_has_media('home-origin-ensenada')) : // Foto histórica de Ensenada, solo si está autorizada. ?>
            <?php bn_media('home-origin-ensenada', 'Historic photograph of Ensenada, Baja California', array('class' => 'aspect-[4/3] grayscale', 'sizes' => '(min-width: 1024px) 25vw, 60vw')); ?>
            <figcaption class="absolute bottom-3 right-5 text-lg italic text-cocoa">Ensenada, B.C.</figcaption>
          <?php else : ?>
            <?php bn_media('home-origin-kitchen', 'Preparing a Bocanegra cheesecake', array('class' => 'aspect-[4/3]', 'sizes' => '(min-width: 1024px) 25vw, 60vw')); ?>
          <?php endif; ?>
        </figure>
        </div>
        <div class="absolute bottom-0 right-0 w-[46%]" data-parallax="0.1">
        <figure data-reveal="right" class="rotate-3 bg-cream p-3 pb-10 shadow-2xl shadow-black/40" style="transition-delay: 0.15s">
          <?php bn_media('home-origin-hands', 'Hands whisking the batter for a Bocanegra cheesecake', array('class' => 'aspect-[3/4]', 'sizes' => '(min-width: 1024px) 18vw, 40vw')); ?>
        </figure>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 04 | Three generations, one recipe -->
<section class="bg-cream py-16 md:py-24">
  <div class="bn-container">
    <h2 data-reveal class="bn-h2 text-center">Three Hands. One Recipe.</h2>
    <ol data-reveal-stagger class="mt-10 grid gap-3 md:grid-cols-3 md:gap-4">
      <?php
      $hands = array(
        array('slot' => 'heritage-1', 'n' => '01', 'label' => 'The Beginning', 'alt' => 'Hands preparing the crust, where the family recipe began'),
        array('slot' => 'heritage-2', 'n' => '02', 'label' => 'The Recipe', 'alt' => 'Whisking the cheesecake batter'),
        array('slot' => 'heritage-3', 'n' => '03', 'label' => 'The Next Generation', 'alt' => 'Young hands finishing a Bocanegra cheesecake'),
      );
      foreach ($hands as $hand) :
        ?>
        <li data-tilt class="bn-zoom-hover relative">
          <?php bn_media($hand['slot'], $hand['alt'], array('class' => 'aspect-[16/9] md:aspect-[4/3]', 'tone' => 'dark', 'sizes' => '(min-width: 768px) 33vw, 100vw')); ?>
          <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>
          <p data-tilt-depth class="absolute bottom-0 left-0 z-10 p-5 text-cream">
            <span class="block font-display text-3xl font-medium leading-none"><?php echo esc_html($hand['n']); ?></span>
            <span class="mt-2 block text-xs font-bold uppercase tracking-[0.24em]"><?php echo esc_html($hand['label']); ?></span>
          </p>
        </li>
      <?php endforeach; ?>
    </ol>
    <div class="mt-10 flex items-center gap-5">
      <span aria-hidden="true" data-reveal="line" class="h-px flex-1 bg-chocolate/20"></span>
      <p data-reveal="fade" class="text-center font-display text-xl md:text-2xl">Different Hands. The Same Family Recipe.</p>
      <span aria-hidden="true" data-reveal="line" class="h-px flex-1 bg-chocolate/20"></span>
    </div>
    <p data-reveal class="bn-body mx-auto mt-4 max-w-2xl text-center">What began in a family kitchen has been shared across generations. Today, Bocanegra carries that tradition forward—one cheesecake at a time.</p>
  </div>
</section>

<!-- Producto -->
<section class="bg-cream-200">
  <div class="grid lg:grid-cols-[55fr_45fr]">
    <?php bn_media('home-cheesecake', 'The signature Bocanegra cheesecake on a cake stand', array('class' => 'hidden lg:block lg:h-full lg:min-h-[32rem]', 'sizes' => '55vw', 'reveal' => 'fade')); ?>
    <div class="bn-split-copy text-center lg:text-left">
      <div data-reveal-stagger class="mx-auto max-w-md lg:mx-0">
        <p class="bn-eyebrow justify-center lg:justify-start">The Bocanegra Cheesecake</p>
        <h2 class="bn-h2 mt-5">Our Signature Family Cheesecake.</h2>
        <ul data-reveal-stagger class="mt-9 grid grid-cols-3 divide-x divide-chocolate/15">
          <?php
          $pillars = array(
            array('icon' => 'wheat', 'title' => 'Family Recipe', 'text' => 'Rooted in Ensenada.'),
            array('icon' => 'hat', 'title' => 'Made to Order', 'text' => 'Prepared upon request.'),
            array('icon' => 'heritage', 'title' => 'Generations of Heritage', 'text' => 'A family story carried forward.'),
          );
          foreach ($pillars as $pillar) :
            ?>
            <li class="flex flex-col items-center px-2 text-center sm:px-4">
              <span class="text-gold-700"><?php echo bn_icon($pillar['icon']); ?></span>
              <span class="mt-3 text-[0.65rem] font-bold uppercase leading-snug tracking-[0.18em]"><?php echo esc_html($pillar['title']); ?></span>
              <span class="mt-1 text-xs leading-snug text-cocoa"><?php echo esc_html($pillar['text']); ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
        <a href="<?php echo esc_url(bn_url('cheesecake')); ?>" class="bn-btn bn-btn-primary mt-10">Discover the Cheesecake <?php echo bn_arrow(); ?></a>
      </div>
    </div>
  </div>
</section>

<!-- 05 | Made for your moment -->
<section class="bg-cream">
  <div class="relative grid lg:min-h-[32rem] lg:grid-cols-[42fr_58fr]">
    <?php bn_stamp('sello-ovalado', 'hidden lg:block lg:left-[36%] lg:top-10 lg:w-44 xl:w-52 -rotate-6'); ?>
    <div class="bn-split-copy order-2 lg:order-1">
      <div data-reveal-stagger class="max-w-md">
        <p class="bn-eyebrow">Made to Order</p>
        <h2 class="bn-h2 mt-5">Prepared for the Moments You Share.</h2>
        <p class="mt-5 text-[0.65rem] font-bold uppercase tracking-[0.26em] text-gold-700">Celebrations · Gatherings · Gifts</p>
        <p class="bn-body mt-5">Planning a celebration, a family gathering, or a thoughtful gift? Request your cheesecake ahead of time, and we’ll follow up to confirm the details.</p>
        <a href="<?php echo esc_url(bn_url('request')); ?>" class="bn-btn bn-btn-primary mt-9">Request Your Cheesecake <?php echo bn_arrow(); ?></a>
      </div>
    </div>
    <div class="order-1 lg:order-2 lg:[clip-path:polygon(14%_0,100%_0,100%_100%,0_100%)]">
      <?php bn_media('home-moment', 'A Bocanegra cheesecake shared at a family celebration', array('class' => 'aspect-[4/3] lg:aspect-auto lg:h-full', 'sizes' => '(min-width: 1024px) 58vw, 100vw', 'reveal' => 'fade')); ?>
    </div>
  </div>
</section>

<?php if ($config['reviews']) : ?>
  <!-- 06 | Social proof (solo reseñas verificadas, con permiso y fuente) -->
  <section class="bn-section bg-cream-200">
    <div class="bn-container">
      <p class="bn-eyebrow justify-center">Words From Our Customers</p>
      <h2 class="bn-h2 mt-5 text-center">Stories Worth Sharing.</h2>
      <div data-reveal-stagger class="mt-12 grid gap-5 md:grid-cols-3">
        <?php foreach (array_slice($config['reviews'], 0, 3) as $review) : ?>
          <figure data-tilt class="flex flex-col border border-chocolate/15 bg-cream p-8">
            <blockquote class="flex-1 text-lg italic leading-relaxed">“<?php echo esc_html($review['quote']); ?>”</blockquote>
            <figcaption class="mt-6 text-sm font-bold"><?php echo esc_html($review['author']); ?><span class="font-normal text-cocoa"> · <?php echo esc_html($review['source']); ?></span></figcaption>
          </figure>
        <?php endforeach; ?>
      </div>
      <?php if ($config['reviews_url']) : ?>
        <p class="mt-10 text-center"><a href="<?php echo esc_url($config['reviews_url']); ?>" class="bn-link" target="_blank" rel="noopener">Read More Reviews <?php echo bn_arrow(); ?></a></p>
      <?php endif; ?>
    </div>
  </section>
<?php endif; ?>

<!-- 07 | Where we are based -->
<section class="bn-section border-t border-chocolate/10">
  <div class="bn-container grid items-center gap-10 lg:grid-cols-[45fr_55fr] lg:gap-20">
    <div data-reveal-stagger>
      <p class="bn-eyebrow">Southern California</p>
      <h2 class="bn-h2 mt-5">From Orange County, With a Family Story.</h2>
      <p class="bn-body mt-6">Bocanegra is based in Orange County, California, with Irvine and Mission Viejo at the heart of our local community. Contact us to discuss your order arrangements.</p>
      <a href="<?php echo esc_url(bn_url('request')); ?>" class="bn-link mt-8">Ask About Your Order <?php echo bn_arrow(); ?></a>
    </div>
    <?php get_template_part('template-parts/oc-map'); ?>
  </div>
</section>

<!-- 08 | Home FAQ -->
<section class="bn-section bg-butter">
  <div class="bn-container grid gap-10 lg:grid-cols-[1fr_2fr] lg:gap-20">
    <div data-reveal-stagger>
      <p class="bn-eyebrow">FAQ</p>
      <h2 class="bn-h2 mt-5">A Few Things to Know.</h2>
      <?php
      get_template_part('template-parts/sticker-cluster', null, array(
        'class' => 'mt-14 hidden max-w-sm lg:flex',
        'items' => array(
          array('sello-circular', 'w-36 -rotate-6'),
          array('etiqueta-slogan', 'ml-4 mt-10 w-48 rotate-3'),
          array('sello-triangular', 'ml-20 mt-2 w-28 -rotate-12'),
        ),
      ));
      ?>
    </div>
    <?php
    get_template_part('template-parts/faq-list', null, array('items' => array(
      array('q' => 'Are Bocanegra cheesecakes made to order?', 'a' => 'Yes. Every order is requested in advance.'),
      array('q' => 'How much notice is needed?', 'a' => 'Please request your cheesecake at least 24 hours ahead of your preferred date and time.'),
      array('q' => 'Where is Bocanegra based?', 'a' => 'We are based in Orange County, California, primarily in the Irvine–Mission Viejo area.'),
      array('q' => 'Is my order confirmed when I submit the form?', 'a' => 'Not yet. Our team will contact you to confirm availability and order details.'),
    )));
    ?>
  </div>
</section>

<!-- 09 | Final conversion + short request form -->
<section id="request-form" class="bn-section">
  <div class="bn-container grid items-start gap-10 lg:grid-cols-[5fr_7fr] lg:gap-16">
    <div data-reveal-stagger class="lg:sticky lg:top-28">
      <p class="bn-eyebrow">Make It Part of Your Story</p>
      <h2 class="bn-h2 mt-5">Let’s Make Your Cheesecake Request.</h2>
      <p class="bn-body mt-6">Tell us when you need it and how we can reach you. We’ll be in touch to confirm the details.</p>
      <?php if ($config['phone'] || $config['email']) : ?>
        <ul class="mt-6 space-y-2">
          <?php if ($config['phone']) : ?>
            <li><a href="tel:<?php echo esc_attr($config['phone']); ?>" class="bn-link">Call <?php echo esc_html($config['phone_display'] ?: $config['phone']); ?></a></li>
          <?php endif; ?>
          <?php if ($config['email']) : ?>
            <li><a href="mailto:<?php echo esc_attr($config['email']); ?>" class="bn-link">Email <?php echo esc_html($config['email']); ?></a></li>
          <?php endif; ?>
        </ul>
      <?php endif; ?>
      <?php bn_media('home-request', 'A Bocanegra cheesecake in its box, ready for an order', array('class' => 'mt-10 hidden aspect-[4/3] lg:block')); ?>
    </div>
    <div data-reveal>
      <?php get_template_part('template-parts/contact-form', null, array('variant' => 'home')); ?>
    </div>
  </div>
</section>

<?php get_footer();

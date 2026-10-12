<?php

get_header(); ?>

<div class="bn-container py-16 md:py-24">
  <div class="mx-auto max-w-3xl">
    <?php if (have_posts()) : ?>
      <?php while (have_posts()) : the_post(); ?>
        <article class="border-b border-chocolate/15 py-8">
          <h2 class="font-display text-3xl font-medium"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
          <div class="prose mt-4 max-w-none"><?php the_excerpt(); ?></div>
        </article>
      <?php endwhile; ?>
    <?php else : ?>
      <p class="bn-body">Nothing to show here yet.</p>
    <?php endif; ?>
  </div>
</div>

<?php get_footer();

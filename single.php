<?php

get_header(); ?>

<div class="bn-container py-16 md:py-24">
  <?php while (have_posts()) : the_post(); ?>
    <article class="mx-auto max-w-3xl">
      <h1 class="bn-h1"><?php the_title(); ?></h1>
      <div class="prose prose-lg mt-10 max-w-none prose-headings:font-display prose-headings:text-chocolate prose-a:text-chocolate prose-a:decoration-gold">
        <?php the_content(); ?>
      </div>
    </article>
  <?php endwhile; ?>
</div>

<?php get_footer();

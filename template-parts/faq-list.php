<?php
/**
 * Acordeón accesible con <details>: el texto de cada respuesta queda en el HTML (AEO).
 * Uso: get_template_part('template-parts/faq-list', null, array('items' => array(array('q' => '', 'a' => ''))));
 */
$items = $args['items'] ?? array();
?>
<div data-reveal-stagger class="divide-y divide-chocolate/15 border-y border-chocolate/15">
  <?php foreach ($items as $item) : ?>
    <details class="group">
      <summary class="flex cursor-pointer list-none items-center justify-between gap-6 py-6 text-left font-display text-2xl font-medium leading-tight md:text-3xl">
        <span><?php echo esc_html($item['q']); ?></span>
        <span aria-hidden="true" class="grid h-10 w-10 shrink-0 place-items-center rounded-full border border-chocolate/25 text-xl transition-transform duration-300 group-open:rotate-45">+</span>
      </summary>
      <p class="bn-body max-w-2xl pb-7 pr-12"><?php echo esc_html($item['a']); ?></p>
    </details>
  <?php endforeach; ?>
</div>

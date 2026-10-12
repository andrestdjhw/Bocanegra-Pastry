<?php
/**
 * Punto de montaje del componente React ContactForm.
 * $args['variant']: 'home' | 'page'
 */
$variant = ($args['variant'] ?? 'page') === 'home' ? 'home' : 'page';
?>
<div data-render-contact-form data-variant="<?php echo esc_attr($variant); ?>">
  <noscript>
    <p class="bg-white p-8 ring-1 ring-chocolate/10">Please enable JavaScript to send your cheesecake request.</p>
  </noscript>
</div>

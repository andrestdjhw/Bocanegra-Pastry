<?php
/**
 * Mapa de Google (iframe) del área de Orange County, solo como contexto.
 * El brief prohíbe pin de tienda o dirección de una cocina privada: se usa una búsqueda
 * del condado (muestra el contorno, sin marcador de negocio). URL en bn_config()['map_embed'].
 *
 * Consentimiento: el iframe no carga (data-src) hasta que el visitante acepta "Maps & Embedded Content"
 * en el aviso de cookies o pulsa "Show Map" (src/scripts/consent.js).
 */
$map = bn_config()['map_embed'];

if (!$map) {
  return;
}

$maps_link = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode('Orange County, California');
?>
<figure data-reveal="zoom" data-bn-map class="bg-cream-200 p-3 ring-1 ring-chocolate/10 md:p-4">
  <div class="relative aspect-[4/3] overflow-hidden bg-cream-300">
    <iframe
      data-src="<?php echo esc_url($map); ?>"
      title="Map of Orange County, California, showing Irvine and Mission Viejo"
      class="bn-map absolute inset-0 h-full w-full border-0"
      loading="lazy"
      referrerpolicy="no-referrer-when-downgrade"
      allowfullscreen
      hidden
    ></iframe>

    <div data-bn-map-placeholder class="absolute inset-0 flex flex-col items-center justify-center gap-4 p-6 text-center">
      <?php echo bn_brand_img('isotipo-chocolate', '', 'h-12 w-auto opacity-80', 'aria-hidden="true" loading="lazy"'); ?>
      <p class="font-display-lg text-3xl font-bold uppercase leading-none text-chocolate">Orange County, California</p>
      <p class="max-w-sm text-sm leading-relaxed text-cocoa">This map is provided by Google, which may set cookies. Show it here or open it in Google Maps.</p>
      <div class="flex flex-wrap items-center justify-center gap-x-5 gap-y-3">
        <button type="button" data-bn-map-load class="bn-btn bn-btn-primary min-h-11 px-5 text-[0.65rem]">Show Map</button>
        <a href="<?php echo esc_url($maps_link); ?>" target="_blank" rel="noopener" class="bn-link text-xs">Open in Google Maps <?php echo bn_arrow(); ?></a>
      </div>
    </div>
  </div>
  <figcaption class="flex flex-wrap items-center justify-between gap-x-6 gap-y-1 px-1 pt-3 text-[0.65rem] font-bold uppercase tracking-[0.22em] text-gold-700">
    <span>Orange County, California</span>
    <span class="font-normal normal-case tracking-normal text-cocoa">Irvine · Mission Viejo — general area shown for reference</span>
  </figcaption>
</figure>

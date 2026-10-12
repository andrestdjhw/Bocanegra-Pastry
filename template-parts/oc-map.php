<?php
/**
 * Mapa de Google (iframe) del área de Orange County, solo como contexto.
 * El brief prohíbe pin de tienda o dirección de una cocina privada: se usa una búsqueda
 * del condado (muestra el contorno, sin marcador de negocio). URL en bn_config()['map_embed'].
 */
$map = bn_config()['map_embed'];

if (!$map) {
  return;
}
?>
<figure data-reveal="zoom" class="bg-cream-200 p-3 ring-1 ring-chocolate/10 md:p-4">
  <div class="relative aspect-[4/3] overflow-hidden bg-cream-300">
    <iframe
      src="<?php echo esc_url($map); ?>"
      title="Map of Orange County, California, showing Irvine and Mission Viejo"
      class="bn-map absolute inset-0 h-full w-full border-0"
      loading="lazy"
      referrerpolicy="no-referrer-when-downgrade"
      allowfullscreen
    ></iframe>
  </div>
  <figcaption class="flex flex-wrap items-center justify-between gap-x-6 gap-y-1 px-1 pt-3 text-[0.65rem] font-bold uppercase tracking-[0.22em] text-gold-700">
    <span>Orange County, California</span>
    <span class="font-normal normal-case tracking-normal text-cocoa">Irvine · Mission Viejo — general area shown for reference</span>
  </figcaption>
</figure>

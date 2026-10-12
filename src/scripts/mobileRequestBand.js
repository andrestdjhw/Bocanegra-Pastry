/**
 * Banda fija en móvil de /request-an-order/: se oculta mientras el formulario
 * o el footer están en pantalla para no tapar campos.
 */
export function initMobileRequestBand() {
  const band = document.querySelector("#bn-mobile-request-band")
  const form = document.querySelector("#request-form")
  if (!band || !form || !("IntersectionObserver" in window)) return

  const footer = document.querySelector("#render-footer-here")
  const visible = new Set()

  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => (entry.isIntersecting ? visible.add(entry.target) : visible.delete(entry.target)))
    const hide = visible.size > 0
    band.classList.toggle("translate-y-full", hide)
    band.toggleAttribute("inert", hide)
  })

  observer.observe(form)
  if (footer) observer.observe(footer)
  band.hidden = false
}

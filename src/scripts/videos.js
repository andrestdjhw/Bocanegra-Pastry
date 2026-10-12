/**
 * Videos de fondo ([data-bg-video]):
 * - No se reproducen con prefers-reduced-motion (queda el poster).
 * - Botón de pausa/play accesible (requisito WCAG para movimiento automático > 5 s).
 * - Se pausan fuera de pantalla para ahorrar batería y datos.
 */
export function initVideos() {
  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches

  document.querySelectorAll("[data-bg-video]").forEach(video => {
    const toggle = video.parentElement.querySelector("[data-bg-video-toggle]")
    let pausedByUser = reduceMotion

    const setPlaying = playing => {
      if (playing) video.play().catch(() => {})
      else video.pause()
      if (!toggle) return
      toggle.setAttribute("aria-label", playing ? "Pause background video" : "Play background video")
      toggle.querySelector('[data-icon="pause"]').classList.toggle("hidden", !playing)
      toggle.querySelector('[data-icon="play"]').classList.toggle("hidden", playing)
    }

    if (reduceMotion) {
      video.removeAttribute("autoplay")
      setPlaying(false)
    }

    if (toggle) {
      toggle.hidden = false
      toggle.addEventListener("click", () => {
        pausedByUser = !video.paused
        setPlaying(!pausedByUser)
      })
    }

    if ("IntersectionObserver" in window) {
      new IntersectionObserver(([entry]) => {
        if (pausedByUser) return
        if (entry.isIntersecting) video.play().catch(() => {})
        else video.pause()
      }).observe(video)
    }
  })
}

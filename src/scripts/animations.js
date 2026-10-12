/**
 * Animaciones de scroll.
 * - [data-reveal]          el elemento aparece al entrar en pantalla y se reinicia al salir (variantes en src/index.css)
 * - [data-reveal-stagger]  sus hijos aparecen en cascada
 * - [data-parallax="0.1"]  desplazamiento suave con el scroll (data-parallax-axis="x" para horizontal)
 * Se desactiva con prefers-reduced-motion (la clase .bn-anim no se agrega en el <head>).
 */
export function initAnimations() {
  window.bnAnimReady = true
  const root = document.documentElement
  if (!root.classList.contains("bn-anim")) return

  const targets = document.querySelectorAll("[data-reveal], [data-reveal-stagger]")

  if (!("IntersectionObserver" in window)) {
    targets.forEach(el => el.classList.add("is-visible"))
    return
  }

  // Entrada: se revela cuando ~12% del elemento ya está dentro de pantalla.
  const enter = new IntersectionObserver(
    entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) entry.target.classList.add("is-visible")
      })
    },
    { rootMargin: "0px 0px -10% 0px", threshold: 0.12 }
  )

  // Salida: se reinicia solo cuando salió por completo, para que vuelva a animarse
  // al regresar (hacia abajo o hacia arriba) sin parpadear mientras sigue visible.
  const exit = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) entry.target.classList.remove("is-visible")
    })
  })

  targets.forEach(el => {
    enter.observe(el)
    exit.observe(el)
  })

  initReplay()
  initParallax()
}

/**
 * Las animaciones de carga del hero (.bn-hero-in, .bn-hero-media) son keyframes CSS;
 * para repetirlas se quita la clase al salir de pantalla y se vuelve a poner al entrar.
 */
function initReplay() {
  const items = document.querySelectorAll(".bn-hero-in, .bn-hero-media")

  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      const el = entry.target
      const cls = el.dataset.replayClass
      if (entry.isIntersecting) {
        if (!el.classList.contains(cls)) {
          void el.offsetWidth // fuerza reflow para reiniciar el keyframe
          el.classList.add(cls)
        }
      } else {
        el.classList.remove(cls)
      }
    })
  })

  items.forEach(el => {
    el.dataset.replayClass = el.classList.contains("bn-hero-in") ? "bn-hero-in" : "bn-hero-media"
    observer.observe(el)
  })
}

function initParallax() {
  const items = [...document.querySelectorAll("[data-parallax]")]
  if (!items.length) return

  let ticking = false

  const update = () => {
    const vh = window.innerHeight
    items.forEach(el => {
      const rect = el.getBoundingClientRect()
      if (rect.bottom < -200 || rect.top > vh + 200) return
      // -1 cuando el centro del elemento está abajo de la pantalla, +1 arriba.
      const progress = (vh / 2 - (rect.top + rect.height / 2)) / vh
      const offset = progress * (parseFloat(el.dataset.parallax) || 0.1) * 200
      const axis = el.dataset.parallaxAxis === "x" ? "X" : "Y"
      el.style.transform = `translate${axis}(${offset.toFixed(1)}px)`
    })
    ticking = false
  }

  const onScroll = () => {
    if (ticking) return
    ticking = true
    requestAnimationFrame(update)
  }

  update()
  window.addEventListener("scroll", onScroll, { passive: true })
  window.addEventListener("resize", onScroll)
}

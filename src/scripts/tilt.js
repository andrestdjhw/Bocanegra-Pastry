/**
 * Efecto 3D al pasar el mouse sobre tarjetas ([data-tilt]):
 * inclinación según la posición del cursor, sombra dinámica hacia el lado opuesto,
 * brillo que sigue al puntero y profundidad para hijos con [data-tilt-depth].
 * Solo con mouse (hover + puntero fino) y sin prefers-reduced-motion.
 */
const MAX_TILT = 8 // grados
const SHADOW = "71 45 32" // café Bocanegra (#472D20) en RGB
const SHADOW_ON_DARK = "20 12 8" // más profunda sobre secciones café, donde la café no se distingue

export function initTilt() {
  const canHover = window.matchMedia("(hover: hover) and (pointer: fine)").matches
  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches
  if (!canHover || reduceMotion) return

  document.querySelectorAll("[data-tilt]").forEach(card => {
    const glare = document.createElement("span")
    glare.className = "bn-tilt-glare"
    glare.setAttribute("aria-hidden", "true")
    card.appendChild(glare)

    let frame = 0
    const onDark = !!card.parentElement.closest(".bg-espresso")
    const shadow = onDark ? `${SHADOW_ON_DARK} / 0.6` : `${SHADOW} / 0.45`

    const onMove = e => {
      cancelAnimationFrame(frame)
      frame = requestAnimationFrame(() => {
        const rect = card.getBoundingClientRect()
        const x = (e.clientX - rect.left) / rect.width // 0 → 1
        const y = (e.clientY - rect.top) / rect.height
        const rotateY = (x - 0.5) * 2 * MAX_TILT
        const rotateX = (0.5 - y) * 2 * MAX_TILT

        // Transición corta mientras se mueve; la inline gana a las transiciones del revelado.
        card.style.transition = "transform 0.12s ease-out, box-shadow 0.3s ease-out"
        card.style.transform = `perspective(900px) rotateX(${rotateX.toFixed(2)}deg) rotateY(${rotateY.toFixed(2)}deg) scale(1.03)`
        card.style.boxShadow = `${(-rotateY * 2).toFixed(1)}px ${(rotateX * 2 + 18).toFixed(1)}px 40px -12px rgb(${shadow})`
        card.style.setProperty("--bn-glare-x", `${(x * 100).toFixed(1)}%`)
        card.style.setProperty("--bn-glare-y", `${(y * 100).toFixed(1)}%`)
      })
    }

    const onEnter = () => card.classList.add("is-tilting")

    const onLeave = () => {
      cancelAnimationFrame(frame)
      card.classList.remove("is-tilting")
      card.style.transition = "transform 0.6s cubic-bezier(0.2, 0.7, 0.2, 1), box-shadow 0.6s ease-out"
      card.style.transform = ""
      card.style.boxShadow = ""
      // Al terminar de volver, devuelve el control al CSS (animaciones de revelado).
      setTimeout(() => {
        if (!card.classList.contains("is-tilting")) card.style.transition = ""
      }, 600)
    }

    card.addEventListener("pointerenter", onEnter)
    card.addEventListener("pointermove", onMove)
    card.addEventListener("pointerleave", onLeave)
  })
}

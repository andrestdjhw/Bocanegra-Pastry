/**
 * Google reCAPTCHA v3, cargado solo cuando alguien empieza a llenar el formulario
 * (evita ~150 KB de Google en cada visita). El token lo verifica WordPress.
 */
let loading = null

export function loadRecaptcha(siteKey) {
  if (!siteKey) return Promise.resolve(null)
  if (loading) return loading

  loading = new Promise(resolve => {
    const script = document.createElement("script")
    script.src = `https://www.google.com/recaptcha/api.js?render=${encodeURIComponent(siteKey)}`
    script.async = true
    script.onload = () => window.grecaptcha.ready(() => resolve(window.grecaptcha))
    script.onerror = () => resolve(null) // bloqueado o sin red: el servidor decide
    document.head.appendChild(script)
  })

  return loading
}

// Devuelve '' si reCAPTCHA no está disponible; nunca bloquea el envío por sí mismo.
export async function getRecaptchaToken(siteKey, action = "order_request") {
  const grecaptcha = await Promise.race([loadRecaptcha(siteKey), new Promise(r => setTimeout(() => r(null), 8000))])
  if (!grecaptcha) return ""

  try {
    return await grecaptcha.execute(siteKey, { action })
  } catch {
    return ""
  }
}

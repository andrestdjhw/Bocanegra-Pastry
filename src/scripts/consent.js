/**
 * Consentimiento de cookies.
 * Categorías:
 *  - essential (siempre activas): reCAPTCHA del formulario, límite de EmailJS y esta misma preferencia.
 *  - maps: Google Maps embebido (bloqueado hasta que se acepte).
 *  - analytics: Google Tag Manager, solo si hay un GTM ID configurado en bn_config().
 * La elección se guarda en la cookie de primera parte "bn_consent" durante 180 días.
 */
const COOKIE = "bn_consent"
const VERSION = 1
const MAX_AGE = 60 * 60 * 24 * 180

export const CONSENT_EVENT = "bn:consent"
export const OPEN_SETTINGS_EVENT = "bn:open-cookie-settings"

// Señal Global Privacy Control del navegador: la analítica queda desactivada.
export const gpcEnabled = () => navigator.globalPrivacyControl === true

export function readConsent() {
  const match = document.cookie.match(/(?:^|; )bn_consent=([^;]*)/)
  if (!match) return null
  try {
    const consent = JSON.parse(decodeURIComponent(match[1]))
    return consent.v === VERSION ? consent : null
  } catch {
    return null
  }
}

export function saveConsent({ maps, analytics }) {
  const consent = { v: VERSION, maps: !!maps, analytics: !!analytics && !gpcEnabled(), ts: Date.now() }
  const secure = window.location.protocol === "https:" ? "; Secure" : ""
  document.cookie = `${COOKIE}=${encodeURIComponent(JSON.stringify(consent))}; Max-Age=${MAX_AGE}; Path=/; SameSite=Lax${secure}`
  window.dispatchEvent(new CustomEvent(CONSENT_EVENT, { detail: consent }))
  return consent
}

/* ─── Google Maps ──────────────────────────────────────────────── */

function applyMaps(consent) {
  document.querySelectorAll("[data-bn-map]").forEach(map => {
    const iframe = map.querySelector("iframe[data-src]")
    const placeholder = map.querySelector("[data-bn-map-placeholder]")
    if (!iframe) return

    if (consent?.maps) {
      if (!iframe.getAttribute("src")) iframe.src = iframe.dataset.src
      iframe.hidden = false
      if (placeholder) placeholder.hidden = true
    } else {
      iframe.removeAttribute("src") // retirar el consentimiento descarga el mapa
      iframe.hidden = true
      if (placeholder) placeholder.hidden = false
    }
  })
}

/* ─── Google Tag Manager ───────────────────────────────────────── */

let gtmLoaded = false

function applyAnalytics(consent, gtmId) {
  if (!gtmId) return

  if (consent?.analytics && !gtmLoaded) {
    gtmLoaded = true
    window.dataLayer = window.dataLayer || []
    window.dataLayer.push({ "gtm.start": Date.now(), event: "gtm.js" })
    const script = document.createElement("script")
    script.async = true
    script.src = `https://www.googletagmanager.com/gtm.js?id=${encodeURIComponent(gtmId)}`
    document.head.appendChild(script)
  } else if (!consent?.analytics && gtmLoaded) {
    window.location.reload() // GTM no se puede descargar: se recarga sin él
  }
}

export function initConsentGates(data) {
  const gtmId = data.consent?.gtmId || ""
  const apply = consent => {
    applyMaps(consent)
    applyAnalytics(consent, gtmId)
  }

  apply(readConsent())
  window.addEventListener(CONSENT_EVENT, e => apply(e.detail))

  // "Show Map" en el mapa bloqueado: acepta solo esa categoría y conserva lo demás.
  document.addEventListener("click", e => {
    if (!e.target.closest("[data-bn-map-load]")) return
    const current = readConsent()
    saveConsent({ maps: true, analytics: current?.analytics })
  })

  // Cualquier elemento con data-bn-cookie-settings abre el panel (footer, Privacy Policy).
  document.addEventListener("click", e => {
    const trigger = e.target.closest("[data-bn-cookie-settings]")
    if (!trigger) return
    e.preventDefault()
    window.dispatchEvent(new CustomEvent(OPEN_SETTINGS_EVENT, { detail: { opener: trigger } }))
  })
}

import React, { useEffect, useRef, useState } from "react"
import { CONSENT_EVENT, OPEN_SETTINGS_EVENT, gpcEnabled, readConsent, saveConsent } from "./consent"

/**
 * Aviso de cookies: tarjeta flotante no bloqueante (el brief pide "sin popups invasivos")
 * + modal "Cookie Settings" con interruptores por categoría, que también abre el footer.
 * "Accept All" y "Reject Non-Essential" tienen el mismo peso visual y el mismo número de pasos.
 */
function Switch({ id, checked, disabled, onChange, label }) {
  return (
    <button
      id={id}
      type="button"
      role="switch"
      aria-checked={checked}
      aria-label={label}
      disabled={disabled}
      onClick={() => onChange(!checked)}
      className={`relative inline-flex h-7 w-12 shrink-0 items-center rounded-full transition-colors duration-200 disabled:cursor-not-allowed ${
        checked ? "bg-chocolate" : "bg-chocolate/20"
      } ${disabled ? "opacity-60" : ""}`}
    >
      <span className={`inline-block h-5 w-5 rounded-full bg-cream shadow transition-transform duration-200 ${checked ? "translate-x-6" : "translate-x-1"}`} />
    </button>
  )
}

function Category({ id, title, badge, description, checked, disabled, onChange }) {
  return (
    <li className="flex items-start justify-between gap-6 border-t border-chocolate/10 py-5">
      <div>
        <p className="flex flex-wrap items-center gap-2 font-bold text-chocolate">
          <label htmlFor={id}>{title}</label>
          {badge && <span className="bg-butter px-2 py-0.5 text-[0.65rem] font-bold uppercase tracking-[0.14em] text-chocolate">{badge}</span>}
        </p>
        <p className="mt-1 text-sm leading-relaxed text-cocoa">{description}</p>
      </div>
      <Switch id={id} checked={checked} disabled={disabled} onChange={onChange} label={title} />
    </li>
  )
}

function CookieConsent({ data }) {
  const hasAnalytics = !!data.consent?.gtmId
  const gpc = gpcEnabled()

  const [consent, setConsent] = useState(() => readConsent())
  const [open, setOpen] = useState(false)
  const [choices, setChoices] = useState({ maps: false, analytics: false })

  const dialogRef = useRef(null)
  const openerRef = useRef(null)

  // Mantiene el estado sincronizado si otro control (ej. "Show Map") guarda una elección.
  useEffect(() => {
    const onConsent = e => setConsent(e.detail)
    const onOpen = e => {
      openerRef.current = e.detail?.opener || document.activeElement
      const current = readConsent()
      setChoices({ maps: !!current?.maps, analytics: !!current?.analytics })
      setOpen(true)
    }
    window.addEventListener(CONSENT_EVENT, onConsent)
    window.addEventListener(OPEN_SETTINGS_EVENT, onOpen)
    return () => {
      window.removeEventListener(CONSENT_EVENT, onConsent)
      window.removeEventListener(OPEN_SETTINGS_EVENT, onOpen)
    }
  }, [])

  // Modal accesible: foco inicial, foco atrapado, Escape para cerrar y bloqueo del scroll de fondo.
  useEffect(() => {
    if (!open) return
    const dialog = dialogRef.current
    const focusables = () => [...dialog.querySelectorAll("button:not([disabled]), a[href]")]
    focusables()[0]?.focus()
    document.documentElement.style.overflow = "hidden"

    const onKey = e => {
      if (e.key === "Escape") return close()
      if (e.key !== "Tab") return
      const items = focusables()
      const first = items[0]
      const last = items[items.length - 1]
      if (e.shiftKey && document.activeElement === first) {
        e.preventDefault()
        last.focus()
      } else if (!e.shiftKey && document.activeElement === last) {
        e.preventDefault()
        first.focus()
      }
    }
    document.addEventListener("keydown", onKey)
    return () => {
      document.removeEventListener("keydown", onKey)
      document.documentElement.style.overflow = ""
    }
  }, [open])

  const close = () => {
    setOpen(false)
    requestAnimationFrame(() => openerRef.current?.focus?.())
  }

  const save = next => {
    setConsent(saveConsent(next))
    close()
  }

  const acceptAll = () => save({ maps: true, analytics: hasAnalytics })
  const rejectAll = () => save({ maps: false, analytics: false })

  const openSettings = e => {
    openerRef.current = e.currentTarget
    setChoices({ maps: !!consent?.maps, analytics: !!consent?.analytics })
    setOpen(true)
  }

  const extras = hasAnalytics ? "Google Maps and analytics" : "Google Maps"

  return (
    <>
      {/* Tarjeta flotante: solo en la primera visita (o si la preferencia expiró). */}
      {!consent && !open && (
        <section
          aria-label="Cookie consent"
          className="bn-cookie-card fixed inset-x-3 bottom-3 z-[70] border border-chocolate/15 bg-cream p-5 text-chocolate shadow-[0_18px_50px_-12px_rgb(34_22_14/0.45)] sm:inset-x-auto sm:bottom-5 sm:left-5 sm:max-w-sm sm:p-6"
        >
          <div className="flex items-start gap-4">
            {data.brandAssets?.isotipo && <img src={data.brandAssets.isotipo} alt="" aria-hidden="true" className="mt-1 h-9 w-auto shrink-0" />}
            <div>
              <p className="font-display-lg text-2xl font-bold uppercase leading-none">We Use Cookies</p>
              <p className="mt-3 text-sm leading-relaxed text-cocoa">
                We use essential cookies to run our website and protect our request form. With your permission, we’ll also load {extras}.{" "}
                {data.privacyUrl && (
                  <a href={data.privacyUrl} className="underline underline-offset-2">
                    Privacy Policy
                  </a>
                )}
              </p>
            </div>
          </div>
          <div className="mt-5 grid grid-cols-2 gap-2">
            <button type="button" onClick={acceptAll} className="bn-btn bn-btn-primary min-h-11 px-3 text-[0.65rem]">
              Accept All
            </button>
            <button type="button" onClick={rejectAll} className="bn-btn bn-btn-outline min-h-11 px-3 text-[0.65rem]">
              Reject Non-Essential
            </button>
          </div>
          <button type="button" onClick={openSettings} className="mt-3 w-full text-center text-xs font-bold uppercase tracking-[0.16em] text-gold-700 underline-offset-4 hover:underline">
            Customize
          </button>
        </section>
      )}

      {/* Modal Cookie Settings */}
      {open && (
        <div className="bn-cookie-backdrop fixed inset-0 z-[80] flex items-end justify-center bg-chocolate-900/55 p-3 backdrop-blur-[2px] sm:items-center sm:p-6" onMouseDown={e => e.target === e.currentTarget && close()}>
          <div
            ref={dialogRef}
            role="dialog"
            aria-modal="true"
            aria-labelledby="bn-cookie-title"
            aria-describedby="bn-cookie-intro"
            className="bn-cookie-dialog max-h-[90vh] w-full max-w-lg overflow-y-auto bg-cream p-6 text-chocolate shadow-2xl sm:p-8"
          >
            <div className="flex items-start justify-between gap-4">
              <h2 id="bn-cookie-title" className="font-display-lg text-4xl font-bold uppercase leading-none">
                Cookie Settings
              </h2>
              <button type="button" onClick={close} aria-label="Close cookie settings" className="grid h-10 w-10 shrink-0 place-items-center rounded-full transition hover:bg-cream-200">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" className="h-5 w-5">
                  <path d="M6 6l12 12M18 6L6 18" />
                </svg>
              </button>
            </div>
            <p id="bn-cookie-intro" className="mt-4 text-sm leading-relaxed text-cocoa">
              Choose which cookies and similar technologies we can use. Essential ones are always on because the website needs them to work.
            </p>

            <ul className="mt-6 border-b border-chocolate/10">
              <Category
                id="bn-cookie-essential"
                title="Essential"
                badge="Always on"
                description="Keep the website and request form working and secure: spam protection with Google reCAPTCHA (loaded when you start filling out the form), EmailJS limits that prevent repeated submissions, and remembering your cookie choices."
                checked
                disabled
                onChange={() => {}}
              />
              <Category
                id="bn-cookie-maps"
                title="Maps & Embedded Content"
                description="Loads the Google Map of our service area. Google may set cookies and collect information such as your IP address."
                checked={choices.maps}
                onChange={maps => setChoices(prev => ({ ...prev, maps }))}
              />
              {hasAnalytics && (
                <Category
                  id="bn-cookie-analytics"
                  title="Analytics"
                  description={
                    gpc
                      ? "Your browser sent a Global Privacy Control signal, so analytics stays off."
                      : "Helps us understand how visitors use our website through Google Tag Manager and Google Analytics."
                  }
                  checked={choices.analytics && !gpc}
                  disabled={gpc}
                  onChange={analytics => setChoices(prev => ({ ...prev, analytics }))}
                />
              )}
            </ul>

            {data.privacyUrl && (
              <p className="mt-5 text-xs leading-relaxed text-cocoa">
                Learn more in our{" "}
                <a href={data.privacyUrl} className="underline underline-offset-2">
                  Privacy Policy
                </a>
                .
              </p>
            )}

            <div className="mt-6 grid gap-2 sm:grid-cols-3">
              <button type="button" onClick={rejectAll} className="bn-btn bn-btn-outline min-h-11 px-3 text-[0.65rem]">
                Reject Non-Essential
              </button>
              <button type="button" onClick={() => save(choices)} className="bn-btn bn-btn-outline min-h-11 px-3 text-[0.65rem]">
                Save Preferences
              </button>
              <button type="button" onClick={acceptAll} className="bn-btn bn-btn-primary min-h-11 px-3 text-[0.65rem]">
                Accept All
              </button>
            </div>
          </div>
        </div>
      )}
    </>
  )
}

export default CookieConsent

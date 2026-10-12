import React, { useEffect, useRef, useState } from "react"

function Brand({ data }) {
  return (
    <a href={data.homeUrl} className="flex shrink-0 flex-col leading-none" aria-label={`${data.brand} home`}>
      {data.logos.header ? (
        <img src={data.logos.header} alt="" width="480" height="131" className="h-9 w-auto sm:h-11 lg:h-13" />
      ) : (
        <>
          <span className="bn-wordmark text-[1.65rem]">Bocanegra</span>
          <span className="mt-1 text-center text-[0.55rem] font-bold uppercase tracking-[0.34em] text-gold-700">Pastry Boutique</span>
        </>
      )}
    </a>
  )
}

function Navbar({ data }) {
  const [open, setOpen] = useState(false)
  const [scrolled, setScrolled] = useState(false)
  const toggleRef = useRef(null)
  const panelRef = useRef(null)

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 8)
    onScroll()
    window.addEventListener("scroll", onScroll, { passive: true })
    return () => window.removeEventListener("scroll", onScroll)
  }, [])

  // Cerrar el menú móvil al pasar a desktop.
  useEffect(() => {
    const mq = window.matchMedia("(min-width: 1024px)")
    const onChange = e => e.matches && setOpen(false)
    mq.addEventListener("change", onChange)
    return () => mq.removeEventListener("change", onChange)
  }, [])

  useEffect(() => {
    if (!open) return
    const onKey = e => {
      if (e.key === "Escape") {
        setOpen(false)
        toggleRef.current?.focus()
      }
    }
    document.addEventListener("keydown", onKey)
    panelRef.current?.querySelector("a")?.focus({ preventScroll: true })
    return () => document.removeEventListener("keydown", onKey)
  }, [open])

  // inert (en vez de hidden) permite animar el panel y lo saca del foco/lectores cuando está cerrado.
  useEffect(() => {
    if (panelRef.current) panelRef.current.inert = !open
  }, [open])

  const solid = scrolled || open

  return (
    <header className={`border-b transition-colors duration-300 ${solid ? "border-chocolate/10 bg-cream/95 shadow-[0_8px_30px_-12px_rgb(34_22_14/0.25)] backdrop-blur" : "border-transparent bg-cream"}`}>
      <nav aria-label="Main" className="bn-container flex h-18 items-center justify-between gap-4">
        <Brand data={data} />

        <ul className="hidden items-center gap-8 lg:flex">
          {data.nav.map(item => (
            <li key={item.key}>
              <a
                href={item.href}
                aria-current={data.current === item.key ? "page" : undefined}
                className={`text-sm underline-offset-8 transition hover:text-gold-700 ${
                  data.current === item.key ? "underline decoration-gold decoration-1" : ""
                }`}
              >
                {item.label}
              </a>
            </li>
          ))}
        </ul>

        <div className="flex items-center gap-2">
          <a href={data.cta.href} className="bn-btn bn-btn-primary min-h-10 px-4 py-2 text-[0.65rem] sm:px-5">
            <span>
              Request<span className="hidden sm:inline"> a Cheesecake</span>
            </span>
          </a>
          <button
            ref={toggleRef}
            type="button"
            className="grid h-11 w-11 place-items-center rounded-full text-chocolate transition hover:bg-cream-200 lg:hidden"
            aria-expanded={open}
            aria-controls="bn-mobile-menu"
            aria-label={open ? "Close menu" : "Open menu"}
            onClick={() => setOpen(prev => !prev)}
          >
            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" className="h-6 w-6">
              {open ? <path d="M6 6l12 12M18 6L6 18" /> : <path d="M4 7h16M4 12h16M4 17h16" />}
            </svg>
          </button>
        </div>
      </nav>

      <div
        id="bn-mobile-menu"
        ref={panelRef}
        className={`grid transition-[grid-template-rows,opacity] duration-300 ease-out lg:hidden ${
          open ? "grid-rows-[1fr] border-t border-chocolate/10 opacity-100" : "grid-rows-[0fr] opacity-0"
        }`}
      >
        <ul className={`bn-container flex min-h-0 flex-col overflow-hidden ${open ? "bn-menu-open py-4" : ""}`}>
          {data.nav.map(item => (
            <li key={item.key}>
              <a
                href={item.href}
                aria-current={data.current === item.key ? "page" : undefined}
                className={`block border-b border-chocolate/10 py-4 font-display text-3xl font-medium ${
                  data.current === item.key ? "text-gold-700" : ""
                }`}
              >
                {item.label}
              </a>
            </li>
          ))}
        </ul>
      </div>
    </header>
  )
}

export default Navbar

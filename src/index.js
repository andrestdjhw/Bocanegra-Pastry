import React from "react"
import ReactDOM from "react-dom/client"
import Topbar from "./scripts/Topbar"
import Navbar from "./scripts/Navbar"
import Footer from "./scripts/Footer"
import ContactForm from "./scripts/ContactForm"
import { initAnalytics } from "./scripts/analytics"
import { initMobileRequestBand } from "./scripts/mobileRequestBand"
import { initAnimations } from "./scripts/animations"
import { initVideos } from "./scripts/videos"
import { initTilt } from "./scripts/tilt"

const data = window.bocanegraData

if (data) {
  initAnalytics(data)

  if (document.querySelector("#render-topbar-here")) {
    const topbarRoot = ReactDOM.createRoot(document.querySelector("#render-topbar-here"))
    topbarRoot.render(<Topbar data={data} />)
  }

  if (document.querySelector("#render-navbar-here")) {
    const navRoot = ReactDOM.createRoot(document.querySelector("#render-navbar-here"))
    navRoot.render(<Navbar data={data} />)
  }

  if (document.querySelector("#render-footer-here")) {
    const footerRoot = ReactDOM.createRoot(document.querySelector("#render-footer-here"))
    footerRoot.render(<Footer data={data} />)
  }

  // Puede haber un formulario por página (Home y Request an Order).
  document.querySelectorAll("[data-render-contact-form]").forEach(el => {
    const formRoot = ReactDOM.createRoot(el)
    formRoot.render(<ContactForm data={data} variant={el.dataset.variant} />)
  })

  initMobileRequestBand()
  initAnimations()
  initVideos()
  initTilt()
}

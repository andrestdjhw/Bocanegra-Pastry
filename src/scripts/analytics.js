/**
 * Eventos GA4/GTM del brief. Se envían a window.dataLayer;
 * GTM decide qué se registra como conversión (solo form_submit_success).
 */
export function track(event, params = {}) {
  window.dataLayer = window.dataLayer || []
  window.dataLayer.push({ event, ...params })
}

const normalizePath = path => path.replace(/\/+$/, "") || "/"

export function initAnalytics(data) {
  const requestPath = normalizePath(new URL(data.cta.href, window.location.href).pathname)
  const storyItem = data.nav.find(item => item.key === "story")
  const storyPath = storyItem ? normalizePath(new URL(storyItem.href, window.location.href).pathname) : null

  document.addEventListener("click", e => {
    const link = e.target.closest("a[href]")
    if (!link) return

    const href = link.getAttribute("href")
    const params = { link_text: link.textContent.trim().slice(0, 80), page: data.current || "other" }

    if (href.startsWith("tel:")) return track("click_to_call", params)
    if (href.startsWith("mailto:")) return track("click_email", params)

    let url
    try {
      url = new URL(link.href)
    } catch {
      return
    }
    if (url.origin !== window.location.origin) return

    const path = normalizePath(url.pathname)
    if (path === requestPath || url.hash === "#request-form") return track("click_request_order", params)
    if (path === storyPath) return track("click_story", params)
  })

  if (data.current === "cheesecake") track("view_cheesecake")
}

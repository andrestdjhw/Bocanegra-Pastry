import React from "react"
import SocialIcons from "./SocialIcons"

function Footer({ data }) {
  const { contact, social } = data
  const year = new Date().getFullYear()

  return (
    <footer className="bn-scallop-top relative overflow-clip bg-espresso text-cream">
      {data.brandAssets.watermark && (
        <img
          src={data.brandAssets.watermark}
          alt=""
          aria-hidden="true"
          loading="lazy"
          className="pointer-events-none absolute -bottom-20 -right-24 w-[38rem] max-w-none opacity-[0.06]"
        />
      )}
      <div className="bn-container relative grid gap-12 py-16 md:grid-cols-[1.4fr_1fr_1fr] md:py-20">
        <div className="max-w-sm">
          {data.logos.footer ? (
            <img src={data.logos.footer} alt={data.brand} width="720" height="365" loading="lazy" className="h-24 w-auto" />
          ) : (
            <p className="flex flex-col leading-none">
              <span className="bn-wordmark text-4xl">Bocanegra</span>
              <span className="mt-2 text-xs font-bold uppercase tracking-[0.32em] text-butter">Pastry Boutique</span>
            </p>
          )}
          {data.brandAssets.slice && (
            <div aria-hidden="true" className="mt-8 flex items-center gap-3">
              {[0, 1, 2].map(i => (
                <img key={i} src={data.brandAssets.slice} alt="" loading="lazy" className="h-5 w-auto opacity-80" />
              ))}
            </div>
          )}
          <p className="mt-3 font-display text-2xl font-semibold uppercase tracking-[0.1em]">{data.slogan}</p>
          <p className="mt-3 leading-relaxed text-cream/75">A family cheesecake recipe, made to order in {data.area}.</p>
          <a href={data.cta.href} className="bn-btn bn-btn-primary mt-8">
            {data.cta.label}
          </a>
        </div>

        <nav aria-labelledby="bn-footer-explore">
          <h2 id="bn-footer-explore" className="text-xs font-bold uppercase tracking-[0.22em] text-butter">
            Explore
          </h2>
          <ul className="mt-5 space-y-3">
            {data.nav.map(item => (
              <li key={item.key}>
                <a href={item.href} className="text-cream/85 transition hover:text-cream hover:underline hover:underline-offset-4">
                  {item.label}
                </a>
              </li>
            ))}
          </ul>

          {(data.privacyUrl || data.termsUrl) && (
            <>
              <h2 className="mt-10 text-xs font-bold uppercase tracking-[0.22em] text-butter">Legal</h2>
              <ul className="mt-5 space-y-3">
                {data.privacyUrl && (
                  <li>
                    <a href={data.privacyUrl} className="text-cream/85 transition hover:text-cream hover:underline hover:underline-offset-4">
                      Privacy Policy
                    </a>
                  </li>
                )}
                {data.termsUrl && (
                  <li>
                    <a href={data.termsUrl} className="text-cream/85 transition hover:text-cream hover:underline hover:underline-offset-4">
                      Terms &amp; Conditions
                    </a>
                  </li>
                )}
              </ul>
            </>
          )}
        </nav>

        <div>
          <h2 className="text-xs font-bold uppercase tracking-[0.22em] text-butter">Service Area</h2>
          <p className="mt-5 leading-relaxed text-cream/85">
            Based in {data.area}, with a focus on {data.focus.join(" and ")}.
          </p>

          {(contact.phone || contact.email) && (
            <ul className="mt-6 space-y-2">
              {contact.phone && (
                <li>
                  <a href={`tel:${contact.phone}`} className="text-cream/85 hover:text-cream hover:underline hover:underline-offset-4">
                    {contact.phoneDisplay}
                  </a>
                </li>
              )}
              {contact.email && (
                <li>
                  <a href={`mailto:${contact.email}`} className="text-cream/85 hover:text-cream hover:underline hover:underline-offset-4">
                    {contact.email}
                  </a>
                </li>
              )}
            </ul>
          )}

          {social.length > 0 && (
            <>
              <h2 className="mt-10 text-xs font-bold uppercase tracking-[0.22em] text-butter">Follow Us</h2>
              <SocialIcons items={social} className="mt-5" />
            </>
          )}
        </div>
      </div>

      <div className="relative border-t border-cream/15">
        <div className="bn-container flex flex-col gap-3 py-6 text-xs text-cream/65 sm:flex-row sm:items-center sm:justify-between">
          <p>
            © {year} {data.brand}. All rights reserved.
          </p>
          <p>
            Site by{" "}
            <a
              href="https://828marketingsolutions.com"
              target="_blank"
              rel="noopener"
              className="text-cream/85 underline-offset-4 transition hover:text-butter hover:underline"
            >
              828 Marketing Solutions
            </a>
          </p>
        </div>
      </div>
    </footer>
  )
}

export default Footer

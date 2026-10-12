import React from "react"
import SocialIcons from "./SocialIcons"

/**
 * Barra superior: contacto a la izquierda y redes sociales a la derecha.
 * Teléfono y correo solo se muestran cuando están verificados en bn_config();
 * mientras tanto se muestra el área de servicio (dato confirmado), sin placeholders.
 */
const ICON = {
  phone: <path d="M6.6 3.5h3l1.5 4-2 1.3a11 11 0 0 0 6.1 6.1l1.3-2 4 1.5v3a2 2 0 0 1-2.2 2A16.5 16.5 0 0 1 4.6 5.7a2 2 0 0 1 2-2.2z" />,
  email: (
    <>
      <rect x="3" y="5" width="18" height="14" rx="2" />
      <path d="m3.5 6.5 8.5 6.5 8.5-6.5" />
    </>
  ),
  pin: (
    <>
      <path d="M12 21s-6.5-5.6-6.5-11a6.5 6.5 0 0 1 13 0c0 5.4-6.5 11-6.5 11z" />
      <circle cx="12" cy="10" r="2.3" />
    </>
  ),
}

function Icon({ name }) {
  return (
    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.7" strokeLinecap="round" strokeLinejoin="round" className="h-4 w-4 shrink-0 text-butter">
      {ICON[name]}
    </svg>
  )
}

const LINK = "inline-flex items-center gap-2 py-2 transition hover:text-butter"

function Topbar({ data }) {
  const { contact, social } = data
  const hasContact = contact.phone || contact.email

  return (
    <div className="bg-espresso text-xs text-cream/85">
      <div className="bn-container flex min-h-10 items-center justify-between gap-4">
        <div className="flex min-w-0 items-center gap-5">
          {hasContact ? (
            <>
              {contact.phone && (
                <a href={`tel:${contact.phone}`} className={LINK} aria-label={`Call ${contact.phoneDisplay}`}>
                  <Icon name="phone" />
                  <span className="hidden sm:inline">{contact.phoneDisplay}</span>
                </a>
              )}
              {contact.email && (
                <a href={`mailto:${contact.email}`} className={LINK} aria-label={`Email ${contact.email}`}>
                  <Icon name="email" />
                  <span className="hidden sm:inline">{contact.email}</span>
                </a>
              )}
            </>
          ) : (
            <p className="flex min-w-0 items-center gap-2 py-2">
              <Icon name="pin" />
              <span className="truncate">
                {data.area}
                <span className="hidden md:inline"> · Made to order with at least 24 hours’ notice</span>
              </span>
            </p>
          )}
        </div>

        {social.length > 0 && (
          <div className="flex shrink-0 items-center gap-3">
            <span className="hidden text-[0.65rem] font-bold uppercase tracking-[0.22em] text-butter lg:inline">Follow Us</span>
            <SocialIcons items={social} size="sm" />
          </div>
        )}
      </div>
    </div>
  )
}

export default Topbar

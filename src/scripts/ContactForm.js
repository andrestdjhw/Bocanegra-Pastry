import React, { useEffect, useRef, useState } from "react"
import { earliestSlot, formatSlot, isAtLeast24hAhead, latestDate } from "./pacificTime"
import { track } from "./analytics"

/**
 * Formulario de solicitud de pedido (no es una compra).
 * variant "home": formulario corto al final de Home.
 * variant "page": formulario principal en /request-an-order/.
 */
const COPY = {
  home: {
    button: "Send Request",
    success: "Thank you. Your request has been received. Your order is not confirmed until our team contacts you.",
  },
  page: {
    button: "Send My Request",
    success:
      "Thank you! We’ve received your cheesecake request. We’ll contact you to confirm availability and next steps. Your order is not yet confirmed.",
  },
}

const EMPTY = { name: "", email: "", phone: "", date: "", time: "", quantity: "1", notes: "", consent: false, company: "" }

const FIELD_ORDER = ["name", "email", "phone", "date", "time", "quantity", "notes", "consent"]

function validate(values, maxQuantity) {
  const errors = {}
  const digits = values.phone.replace(/\D/g, "")
  const quantity = Number(values.quantity)

  if (values.name.trim().length < 2) errors.name = "Please enter your full name."
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(values.email.trim())) errors.email = "Please enter a valid email address."
  if (digits.length < 10 || digits.length > 15) errors.phone = "Please enter a valid phone number, including area code."
  if (!values.date) errors.date = "Please choose your preferred date."
  if (!values.time) errors.time = "Please choose your preferred time."
  if (values.date && values.time && !isAtLeast24hAhead(values.date, values.time)) {
    errors.time = `Please choose a date and time at least 24 hours from now (${formatSlot(earliestSlot())} or later).`
  }
  if (!Number.isInteger(quantity) || quantity < 1 || quantity > maxQuantity) {
    errors.quantity = `Please enter a number between 1 and ${maxQuantity}.`
  }
  if (values.notes.length > 1000) errors.notes = "Please keep your message under 1,000 characters."
  if (!values.consent) errors.consent = "Please confirm we may contact you about your request."

  return errors
}

function Field({ id, label, required, error, hint, children }) {
  return (
    <div>
      <label htmlFor={id} className="block text-sm font-bold text-chocolate">
        {label}
        {required && (
          <span aria-hidden="true" className="text-gold-700">
            {" "}
            *
          </span>
        )}
      </label>
      {hint && (
        <p id={`${id}-hint`} className="mt-1 text-xs leading-relaxed text-cocoa">
          {hint}
        </p>
      )}
      <div className="mt-2">{children}</div>
      {error && (
        <p id={`${id}-error`} className="mt-2 text-sm font-bold text-red-800">
          {error}
        </p>
      )}
    </div>
  )
}

function ContactForm({ data, variant = "page" }) {
  const copy = COPY[variant] || COPY.page
  const { endpoint, maxQuantity } = data.form
  const uid = `bn-${variant}`

  const [values, setValues] = useState(EMPTY)
  const [errors, setErrors] = useState({})
  const [status, setStatus] = useState("idle") // idle | submitting | success
  const [serverError, setServerError] = useState("")
  const [, setTick] = useState(0)

  const formRef = useRef(null)
  const successRef = useRef(null)
  const startedAt = useRef(Date.now())
  const started = useRef(false)

  // Refresca la hora mínima mostrada cada minuto.
  useEffect(() => {
    const id = setInterval(() => setTick(t => t + 1), 60000)
    return () => clearInterval(id)
  }, [])

  useEffect(() => {
    if (status === "success") successRef.current?.focus()
  }, [status])

  const earliest = earliestSlot()

  const set = name => e => {
    const value = e.target.type === "checkbox" ? e.target.checked : e.target.value
    setValues(prev => ({ ...prev, [name]: value }))
    if (errors[name]) setErrors(prev => ({ ...prev, [name]: undefined }))
  }

  const onFocus = () => {
    if (started.current) return
    started.current = true
    track("form_start", { form_variant: variant })
  }

  const focusFirstError = fieldErrors => {
    const first = FIELD_ORDER.find(name => fieldErrors[name])
    if (first) requestAnimationFrame(() => formRef.current?.elements[first]?.focus())
  }

  const onSubmit = async e => {
    e.preventDefault()
    setServerError("")

    const fieldErrors = validate(values, maxQuantity)
    setErrors(fieldErrors)
    if (Object.keys(fieldErrors).length) {
      focusFirstError(fieldErrors)
      return
    }

    setStatus("submitting")

    try {
      const res = await fetch(endpoint, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          ...values,
          name: values.name.trim(),
          email: values.email.trim(),
          quantity: Number(values.quantity),
          source: variant,
          elapsed_ms: Date.now() - startedAt.current,
        }),
      })
      const body = await res.json().catch(() => ({}))

      if (res.ok) {
        setStatus("success")
        track("form_submit_success", { form_variant: variant, quantity: Number(values.quantity) })
        return
      }

      const remoteErrors = body?.data?.fields
      if (remoteErrors) {
        setErrors(remoteErrors)
        focusFirstError(remoteErrors)
      }
      setServerError(body?.message || "Something went wrong while sending your request. Please try again in a few minutes.")
    } catch {
      setServerError("We couldn’t reach our server. Please check your connection and try again.")
    }

    setStatus("idle")
  }

  if (status === "success") {
    return (
      <div ref={successRef} tabIndex={-1} role="status" className="bg-white p-8 shadow-sm ring-1 ring-chocolate/10 focus:outline-none md:p-10">
        <svg aria-hidden="true" viewBox="0 0 48 48" fill="none" stroke="currentColor" strokeWidth="2" className="h-12 w-12 text-gold-700">
          <circle cx="24" cy="24" r="21" />
          <path className="bn-check" d="M15 24.5l6 6 12-13" strokeLinecap="round" strokeLinejoin="round" />
        </svg>
        <p className="mt-6 font-display text-4xl font-medium leading-tight">Request received.</p>
        <p className="mt-3 bn-body">{copy.success}</p>
      </div>
    )
  }

  const describedBy = (name, hint) =>
    [hint && `${uid}-${name}-hint`, errors[name] && `${uid}-${name}-error`].filter(Boolean).join(" ") || undefined

  const inputProps = (name, hint) => ({
    id: `${uid}-${name}`,
    name,
    value: values[name],
    onChange: set(name),
    "aria-invalid": errors[name] ? "true" : undefined,
    "aria-describedby": describedBy(name, hint),
    className: "bn-input",
  })

  return (
    <form
      ref={formRef}
      noValidate
      onSubmit={onSubmit}
      onFocus={onFocus}
      className="relative bg-white p-6 ring-1 ring-chocolate/10 sm:p-8 md:p-10"
    >
      <p className="text-xs text-cocoa">
        Fields marked <span className="text-gold-700">*</span> are required.
      </p>

      <div className="mt-6 grid gap-6 sm:grid-cols-2">
        <div className="sm:col-span-2">
          <Field id={`${uid}-name`} label="Full Name" required error={errors.name}>
            <input {...inputProps("name")} type="text" autoComplete="name" required />
          </Field>
        </div>

        <Field id={`${uid}-email`} label="Email Address" required error={errors.email}>
          <input {...inputProps("email")} type="email" autoComplete="email" inputMode="email" required />
        </Field>

        <Field id={`${uid}-phone`} label="Phone Number" required error={errors.phone}>
          <input {...inputProps("phone")} type="tel" autoComplete="tel" inputMode="tel" required />
        </Field>

        <Field id={`${uid}-date`} label="Preferred Date" required error={errors.date} hint="At least 24 hours from now.">
          <input {...inputProps("date", true)} type="date" min={earliest.date} max={latestDate()} required />
        </Field>

        <Field id={`${uid}-time`} label="Preferred Time" required error={errors.time} hint={`Pacific Time. Earliest: ${formatSlot(earliest)}.`}>
          <input {...inputProps("time", true)} type="time" required />
        </Field>

        <div className="sm:col-span-2">
          <Field id={`${uid}-quantity`} label="Number of Cheesecakes" required error={errors.quantity}>
            <input {...inputProps("quantity")} type="number" inputMode="numeric" min="1" max={maxQuantity} step="1" required className="bn-input sm:max-w-40" />
          </Field>
        </div>

        <div className="sm:col-span-2">
          <Field id={`${uid}-notes`} label="Questions or Special Requests (optional)" error={errors.notes}>
            <textarea {...inputProps("notes")} rows={4} maxLength={1000} />
          </Field>
        </div>
      </div>

      {/* Honeypot antispam: invisible para personas. */}
      <div aria-hidden="true" className="absolute -left-[9999px] h-px w-px overflow-hidden">
        <label htmlFor={`${uid}-company`}>Company</label>
        <input id={`${uid}-company`} name="company" type="text" tabIndex={-1} autoComplete="off" value={values.company} onChange={set("company")} />
      </div>

      <div className="mt-6">
        <div className="flex items-start gap-3">
          <input
            id={`${uid}-consent`}
            name="consent"
            type="checkbox"
            checked={values.consent}
            onChange={set("consent")}
            required
            aria-invalid={errors.consent ? "true" : undefined}
            aria-describedby={errors.consent ? `${uid}-consent-error` : undefined}
            className="mt-1 h-5 w-5 shrink-0 border-chocolate/40 accent-gold-700"
          />
          <label htmlFor={`${uid}-consent`} className="text-sm leading-relaxed text-chocolate">
            I agree to be contacted about my order request.
            <span aria-hidden="true" className="text-gold-700">
              {" "}
              *
            </span>
            {data.privacyUrl && (
              <>
                {" "}
                See our{" "}
                <a href={data.privacyUrl} className="underline underline-offset-4">
                  Privacy Policy
                </a>
                .
              </>
            )}
          </label>
        </div>
        {errors.consent && (
          <p id={`${uid}-consent-error`} className="mt-2 text-sm font-bold text-red-800">
            {errors.consent}
          </p>
        )}
      </div>

      <div role="alert" className="empty:hidden mt-6 rounded-xl bg-red-50 p-4 text-sm font-bold text-red-800">
        {serverError}
      </div>

      <div className="mt-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <button type="submit" disabled={status === "submitting"} className="bn-btn bn-btn-primary w-full disabled:cursor-wait disabled:opacity-70 sm:w-auto">
          {status === "submitting" ? "Sending…" : copy.button}
        </button>
        <p className="text-xs leading-relaxed text-cocoa sm:max-w-56 sm:text-right">Submitting this form does not confirm your order.</p>
      </div>
    </form>
  )
}

export default ContactForm

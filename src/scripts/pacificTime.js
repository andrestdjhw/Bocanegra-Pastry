/**
 * Reglas de fecha/hora del pedido: mínimo 24 horas reales desde ahora,
 * expresado en hora de California (America/Los_Angeles), sin importar
 * la zona horaria del dispositivo. El servidor valida lo mismo.
 */
export const TIME_ZONE = "America/Los_Angeles"

const DAY_MS = 24 * 60 * 60 * 1000

const partsFormatter = new Intl.DateTimeFormat("en-CA", {
  timeZone: TIME_ZONE,
  year: "numeric",
  month: "2-digit",
  day: "2-digit",
  hour: "2-digit",
  minute: "2-digit",
  hourCycle: "h23",
})

export function toPacific(ms) {
  const p = Object.fromEntries(partsFormatter.formatToParts(new Date(ms)).map(part => [part.type, part.value]))
  return { date: `${p.year}-${p.month}-${p.day}`, time: `${p.hour}:${p.minute}` }
}

// Redondea hacia arriba al minuto siguiente para no ofrecer un horario que ya quedó por debajo de 24 h.
export function earliestSlot(now = Date.now()) {
  return toPacific(Math.ceil((now + DAY_MS) / 60000) * 60000)
}

export function latestDate(now = Date.now()) {
  return toPacific(now + 365 * DAY_MS).date
}

export function isAtLeast24hAhead(date, time, now = Date.now()) {
  const min = earliestSlot(now)
  return `${date} ${time}` >= `${min.date} ${min.time}`
}

export function formatSlot({ date, time }) {
  const [y, m, d] = date.split("-").map(Number)
  const [h, min] = time.split(":").map(Number)
  const label = new Date(Date.UTC(y, m - 1, d, h, min)).toLocaleString("en-US", {
    timeZone: "UTC",
    weekday: "short",
    month: "short",
    day: "numeric",
    hour: "numeric",
    minute: "2-digit",
  })
  return `${label} PT`
}

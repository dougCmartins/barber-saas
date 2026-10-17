export function clock(value: string): string {
  return value.slice(11, 16)
}

export function weekdayLabel(value: string): string {
  const formatted = new Intl.DateTimeFormat('pt-BR', { weekday: 'long' }).format(localDateTime(value))
  const word = formatted.split('-')[0] ?? formatted

  return word.charAt(0).toUpperCase() + word.slice(1)
}

export function dayMonth(value: string): string {
  return new Intl.DateTimeFormat('pt-BR', { day: 'numeric', month: 'long' }).format(localDateTime(value))
}

export function dayShortMonth(value: string): string {
  const date = localDateTime(value)
  const month = new Intl.DateTimeFormat('pt-BR', { month: 'short' }).format(date).replace('.', '')

  return `${date.getDate()} ${month}`
}

export function nearestUpcoming<T extends { starts_at: string }>(items: T[], now = new Date()): T | null {
  const upcoming = items
    .filter((item) => localDateTime(item.starts_at).getTime() > now.getTime())
    .sort((left, right) => localDateTime(left.starts_at).getTime() - localDateTime(right.starts_at).getTime())

  return upcoming[0] ?? null
}

export function isPastStart(value: string, now = new Date()): boolean {
  return localDateTime(value).getTime() <= now.getTime()
}

function localDateTime(value: string): Date {
  const [datePart = '', timePart = '00:00:00'] = value.split(' ')
  const [year = 0, month = 1, day = 1] = datePart.split('-').map(Number)
  const [hour = 0, minute = 0, second = 0] = timePart.split(':').map(Number)

  return new Date(year, month - 1, day, hour, minute, second)
}

export function isoDate(date: Date = new Date()): string {
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')

  return `${date.getFullYear()}-${month}-${day}`
}

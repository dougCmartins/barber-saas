import type { AppointmentStatus } from '@/types/appointmentStatus'

export type PeriodPreset = 'this-week' | 'last-week' | 'last-15' | 'last-month'

export const periodPresets = [
  'this-week',
  'last-week',
  'last-15',
  'last-month',
] as const satisfies readonly PeriodPreset[]

export interface FilterableAppointment {
  id: string
  date: Date
  time: string
  barberId: string
  serviceIds: string[]
  status: AppointmentStatus
}

export interface AppointmentFilters {
  period?: PeriodPreset | undefined
  from?: string | undefined
  to?: string | undefined
  statuses: AppointmentStatus[]
  barberIds: string[]
}

export const emptyAppointmentFilters: AppointmentFilters = {
  statuses: [],
  barberIds: [],
}

function startOfDay(date: Date): Date {
  return new Date(date.getFullYear(), date.getMonth(), date.getDate())
}

function addDays(date: Date, days: number): Date {
  const result = new Date(date)
  result.setDate(result.getDate() + days)

  return result
}

export function periodRange(preset: PeriodPreset, today: Date): [Date, Date] {
  const start = startOfDay(today)
  const weekStart = addDays(start, -((start.getDay() + 6) % 7))

  switch (preset) {
    case 'this-week':
      return [weekStart, addDays(weekStart, 6)]
    case 'last-week':
      return [addDays(weekStart, -7), addDays(weekStart, -1)]
    case 'last-15':
      return [addDays(start, -15), start]
    case 'last-month':
      return [addDays(start, -30), start]
  }
}

function parseInput(value: string): Date {
  const [year, month, day] = value.split('-').map(Number)

  return new Date(year!, (month ?? 1) - 1, day ?? 1)
}

export function filterAppointments(
  list: FilterableAppointment[],
  filters: AppointmentFilters,
  today: Date,
): FilterableAppointment[] {
  let from: Date | undefined
  let to: Date | undefined

  if (filters.period) {
    ;[from, to] = periodRange(filters.period, today)
  }

  if (filters.from) {
    from = parseInput(filters.from)
  }

  if (filters.to) {
    to = parseInput(filters.to)
  }

  return list.filter((appointment) => {
    const day = startOfDay(appointment.date)

    if (from && day < from) {
      return false
    }

    if (to && day > to) {
      return false
    }

    if (filters.statuses.length && !filters.statuses.includes(appointment.status)) {
      return false
    }

    if (filters.barberIds.length && !filters.barberIds.includes(appointment.barberId)) {
      return false
    }

    return true
  })
}

import { describe, expect, it } from 'vitest'
import { AppointmentStatus } from '@/types/appointmentStatus'
import {
  emptyAppointmentFilters,
  filterAppointments,
  type FilterableAppointment,
} from '@/utils/appointmentsFilter'

const today = new Date(2026, 9, 17)

const list: FilterableAppointment[] = [
  {
    id: 'a',
    date: new Date(2026, 9, 14),
    time: '10:00',
    barberId: 'rafa',
    serviceIds: [],
    status: AppointmentStatus.Completed,
  },
  {
    id: 'b',
    date: new Date(2026, 9, 8),
    time: '10:00',
    barberId: 'leo',
    serviceIds: [],
    status: AppointmentStatus.Canceled,
  },
  {
    id: 'c',
    date: new Date(2026, 8, 1),
    time: '10:00',
    barberId: 'rafa',
    serviceIds: [],
    status: AppointmentStatus.Missed,
  },
]

describe('filterAppointments', () => {
  it('filters by status', () => {
    // Arrange
    const filters = { ...emptyAppointmentFilters, statuses: [AppointmentStatus.Canceled] }

    // Act
    const result = filterAppointments(list, filters, today).map((item) => item.id)

    // Assert
    expect(result).toEqual(['b'])
  })

  it('keeps only Monday-Sunday of the current week for this-week', () => {
    // Arrange
    const filters = { ...emptyAppointmentFilters, period: 'this-week' as const }

    // Act
    const result = filterAppointments(list, filters, today).map((item) => item.id)

    // Assert
    expect(result).toEqual(['a'])
  })

  it('keeps the previous week for last-week', () => {
    // Arrange
    const filters = { ...emptyAppointmentFilters, period: 'last-week' as const }

    // Act
    const result = filterAppointments(list, filters, today).map((item) => item.id)

    // Assert
    expect(result).toEqual(['b'])
  })

  it('uses custom from/to range', () => {
    // Arrange
    const filters = {
      ...emptyAppointmentFilters,
      from: '2026-09-01',
      to: '2026-09-30',
    }

    // Act
    const result = filterAppointments(list, filters, today).map((item) => item.id)

    // Assert
    expect(result).toEqual(['c'])
  })
})

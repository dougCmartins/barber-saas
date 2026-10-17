import { describe, expect, it } from 'vitest'
import { dayMonth, dayShortMonth, nearestUpcoming, weekdayLabel } from '@/utils/dateTime'

const now = new Date(2026, 9, 12, 12, 0, 0)

describe('dateTime', () => {
  it('formats a thursday in october', () => {
    expect(weekdayLabel('2026-10-15 16:00:00')).toBe('Quinta')
    expect(dayMonth('2026-10-15 16:00:00')).toBe('15 de outubro')
    expect(dayShortMonth('2026-09-28 16:00:00')).toBe('28 set')
  })

  it('returns the nearest future start', () => {
    const items = [
      { id: 1, starts_at: '2026-10-20 09:00:00' },
      { id: 2, starts_at: '2026-10-15 16:00:00' },
      { id: 3, starts_at: '2026-09-28 16:00:00' },
    ]

    expect(nearestUpcoming(items, now)).toEqual(items[1])
  })

  it('returns null when every start is in the past', () => {
    const items = [{ id: 3, starts_at: '2026-09-28 16:00:00' }]

    expect(nearestUpcoming(items, now)).toBeNull()
  })
})

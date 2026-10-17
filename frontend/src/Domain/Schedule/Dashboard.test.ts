import { flushPromises, mount } from '@vue/test-utils'
import { beforeEach, describe, expect, it, vi } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import type { ListedAppointment } from '@/Domain/Schedule/scheduleModel'
import Dashboard from '@/Domain/Schedule/Dashboard.vue'
import { http } from '@/http/client'
import { i18n } from '@/i18n'
import { AppointmentStatus } from '@/types/appointmentStatus'
import { isoDate } from '@/utils/dateTime'

vi.mock('@/http/client', () => ({
  http: vi.fn<(path: string, init?: RequestInit) => Promise<{ data: unknown }>>(),
}))

const httpMock = vi.mocked(http)

const appointment: ListedAppointment = {
  id: 3,
  starts_at: '2026-10-15 10:00:00',
  ends_at: '2026-10-15 10:30:00',
  client_name: 'Ana',
  service_name: 'Haircut',
  professional_name: 'Diego',
  price: 45.5,
  status: AppointmentStatus.Pending,
}

function mountDashboard() {
  const pinia = createPinia()
  setActivePinia(pinia)

  return mount(Dashboard, {
    global: { plugins: [pinia, i18n] },
  })
}

function metric(wrapper: ReturnType<typeof mountDashboard>, label: string) {
  return wrapper.findAll('article').find((card) => card.text().includes(label))
}

describe('Dashboard', () => {
  beforeEach(() => {
    httpMock.mockReset()
    httpMock.mockResolvedValue({ data: [appointment] })
  })

  it('shows today metrics, the agenda row and requests that date', async () => {
    const wrapper = mountDashboard()
    await flushPromises()

    expect(wrapper.text()).toContain('Hoje na Navalha')
    expect(wrapper.text()).toContain('Bom trabalho.')
    expect(metric(wrapper, 'Agendamentos')?.get('.font-display.text-3xl').text()).toBe('1')
    expect(metric(wrapper, 'Agendamentos')?.text()).toContain('1 ainda pendentes')
    expect(metric(wrapper, 'Receita prevista')?.text()).toContain('45,50')
    expect(metric(wrapper, 'Ocupação')?.get('.font-display.text-3xl').text()).toBe('100%')
    expect(metric(wrapper, 'Equipe ativa')?.get('.font-display.text-3xl').text()).toBe('1')
    expect(wrapper.get('table').text()).toContain('Ana')
    expect(wrapper.get('table').text()).toContain('Haircut')
    expect(wrapper.get('table').text()).toContain('Diego')
    expect(wrapper.get('table').text()).toContain('10:00')
    expect(wrapper.get('table tbody span').text()).toBe('Pendente')
    expect(wrapper.get('table tbody span').classes()).toContain('bg-warning/15')
    expect(httpMock).toHaveBeenCalledWith(`/appointments?date=${isoDate()}`)
  })

  it('keeps the agenda table and shows the empty day copy', async () => {
    httpMock.mockResolvedValue({ data: [] })

    const wrapper = mountDashboard()
    await flushPromises()

    expect(wrapper.get('table').exists()).toBe(true)
    expect(wrapper.text()).toContain('Nenhum agendamento neste dia.')
  })

  it('opens the appointment and completes it from the modal', async () => {
    httpMock.mockImplementation(async (_path: string, init?: RequestInit) => {
      if (init?.method === 'PATCH') {
        return { data: { ...appointment, status: AppointmentStatus.Completed } }
      }

      return { data: [appointment] }
    })

    const wrapper = mountDashboard()
    await flushPromises()

    await wrapper.get('table tbody tr').trigger('click')

    expect(wrapper.text()).toContain('Ana')
    expect(wrapper.text()).toContain('Haircut')
    expect(wrapper.text()).toContain('Diego')
    expect(wrapper.text()).toContain('10:00 a 10:30')
    expect(wrapper.text()).toContain('R$')
    expect(wrapper.get('[data-testid="complete-appointment"]').exists()).toBe(true)

    await wrapper.get('[data-testid="complete-appointment"]').trigger('click')
    await flushPromises()

    expect(httpMock).toHaveBeenCalledWith('/appointments/3/complete', { method: 'PATCH' })
    expect(wrapper.find('[data-testid="complete-appointment"]').exists()).toBe(false)
    expect(wrapper.get('table tbody span').text()).toBe('Concluído')
    expect(wrapper.get('table tbody span').classes()).toContain('bg-success/15')
  })

  it('hides the actions when the appointment is already closed', async () => {
    httpMock.mockResolvedValue({ data: [{ ...appointment, status: AppointmentStatus.Completed }] })

    const wrapper = mountDashboard()
    await flushPromises()
    await wrapper.get('table tbody tr').trigger('click')

    expect(wrapper.find('[data-testid="complete-appointment"]').exists()).toBe(false)
    expect(wrapper.find('[data-testid="cancel-appointment"]').exists()).toBe(false)
    expect(wrapper.get('table tbody span').text()).toBe('Concluído')
    expect(wrapper.get('table tbody span').classes()).toContain('bg-success/15')
  })
})

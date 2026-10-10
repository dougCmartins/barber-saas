import { describe, expect, it } from 'vitest'
import { mount } from '@vue/test-utils'
import ServiceCard from '@/Domain/Catalog/ServiceCard.vue'
import type { Service } from '@/Domain/Catalog/serviceModel'
import { i18n } from '@/i18n'

const service: Service = {
  id: 1,
  barbershop_id: null,
  name: 'Corte assinatura',
  description: 'Consultoria rápida, corte e finalização.',
  price: 65,
  duration_in_minutes: 45,
  active: true,
}

describe('ServiceCard', () => {
  it('renders name, price and duration', () => {
    const wrapper = mount(ServiceCard, {
      props: { service },
      global: { plugins: [i18n] },
    })

    expect(wrapper.text()).toContain('Corte assinatura')
    expect(wrapper.text()).toContain('R$ 65')
    expect(wrapper.text()).toContain('45 min')
  })
})

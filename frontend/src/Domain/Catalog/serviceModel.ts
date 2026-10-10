export interface Service {
  id: number
  barbershop_id: number | null
  name: string
  description: string | null
  price: number
  duration_in_minutes: number
  active: boolean
}

export const publicServices: Service[] = [
  {
    id: 1,
    barbershop_id: null,
    name: 'Corte assinatura',
    description: 'Consultoria rápida, corte e finalização.',
    price: 65,
    duration_in_minutes: 45,
    active: true,
  },
  {
    id: 2,
    barbershop_id: null,
    name: 'Degradê premium',
    description: 'Fade preciso com acabamento na navalha.',
    price: 75,
    duration_in_minutes: 50,
    active: true,
  },
  {
    id: 3,
    barbershop_id: null,
    name: 'Barboterapia',
    description: 'Toalha quente, desenho e hidratação.',
    price: 55,
    duration_in_minutes: 35,
    active: true,
  },
  {
    id: 4,
    barbershop_id: null,
    name: 'Ritual completo',
    description: 'Corte assinatura e barboterapia.',
    price: 110,
    duration_in_minutes: 80,
    active: true,
  },
]

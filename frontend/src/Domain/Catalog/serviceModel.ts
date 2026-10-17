export interface Service {
  id: number
  barbershop_id: number | null
  name: string
  description: string | null
  price: number
  duration_in_minutes: number
  active: boolean
}

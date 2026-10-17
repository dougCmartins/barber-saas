export interface Professional {
  id: number
  barbershop_id: number
  user_id: number
  name: string
  specialty: string | null
  avatar_url: string | null
  experience_years: number | null
  active: boolean
}

export interface Barbershop {
  id: number
  name: string
  slug: string
  active: boolean
  address: string | null
  city: string | null
  closing_time: string | null
  rating: number
  total_reviews: number
  banner_url: string | null
}

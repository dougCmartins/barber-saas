import type { Barbershop } from '@/Domain/Tenant/barbershopModel'
import type { UserRole } from '@/types/userRole'

export interface OnboardingRequest {
  shop_name: string
  shop_slug: string
  owner_name: string
  owner_email: string
  owner_password: string
}

export interface OnboardingOwner {
  id: number
  barbershop_id: number | null
  name: string
  email: string
  role: UserRole
}

export interface OnboardingResult {
  shop: Barbershop
  owner: OnboardingOwner
}

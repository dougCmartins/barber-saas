export const UserRole = {
  SuperAdmin: 'super_admin',
  Owner: 'owner',
  Barber: 'barber',
  Receptionist: 'receptionist',
  Client: 'client',
} as const

export type UserRole = (typeof UserRole)[keyof typeof UserRole]

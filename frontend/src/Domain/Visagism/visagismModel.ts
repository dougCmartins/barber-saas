export const HairTexture = {
  Straight: 'straight',
  Wavy: 'wavy',
  Curly: 'curly',
  Coily: 'coily',
} as const

export type HairTexture = (typeof HairTexture)[keyof typeof HairTexture]

export const FaceShape = {
  Oval: 'oval',
  Square: 'square',
  Round: 'round',
  Rectangular: 'rectangular',
  InvertedTriangle: 'inverted_triangle',
  Diamond: 'diamond',
} as const

export type FaceShape = (typeof FaceShape)[keyof typeof FaceShape]

export const BeardDensity = {
  None: 'none',
  Light: 'light',
  Medium: 'medium',
  Full: 'full',
} as const

export type BeardDensity = (typeof BeardDensity)[keyof typeof BeardDensity]

export const ConsultationType = {
  Ai: 'ai',
  Manual: 'manual',
} as const

export type ConsultationType = (typeof ConsultationType)[keyof typeof ConsultationType]

export const VisagismStep = {
  Onboarding: 'onboarding',
  Capture: 'capture',
  Analyzing: 'analyzing',
  Results: 'results',
  ManualForm: 'manual_form',
  ManualDone: 'manual_done',
} as const

export type VisagismStep = (typeof VisagismStep)[keyof typeof VisagismStep]

export interface HaircutSuggestion {
  id: number
  name: string
  match_percentage: number
  justification: string
  image_url: string | null
  service_id: number
  price: number
}

export interface ProductRecommendation {
  id: number
  name: string
  price: number
  microcopy: string
  image_url: string | null
  service_id: number
}

export interface VisagismResult {
  face_shape: FaceShape | string
  face_geometry_summary: string
  hair_type: HairTexture | string
  suggestions: HaircutSuggestion[]
  upsell_products: ProductRecommendation[]
}

export interface ManualConsultation {
  id: number
  consultation_type: 'manual'
  reference_photos: string[]
  desired_notes: string
  disliked_notes: string
}

export interface VisagismTrashedSummary {
  id: number
  consultation_type: ConsultationType
}

export interface VisagismActiveRecord {
  id: number
  consultation_type: ConsultationType
  result?: VisagismResult | null
  manual?: ManualConsultation | null
}

export interface VisagismExistingState {
  active: VisagismActiveRecord | null
  trashed_ai: VisagismTrashedSummary | null
  trashed_manual: VisagismTrashedSummary | null
}

export interface AnalyzeFacePayload {
  image?: string | null
  age?: number | null
  hair_texture?: HairTexture | null
  beard_density?: BeardDensity | null
}

export interface OverrideVisagismPayload {
  face_shape: FaceShape | string
  hair_type: HairTexture | string
  feedback_reason: string
}

export interface SaveManualPayload {
  reference_photos: string[]
  desired_notes: string
  disliked_notes: string
}

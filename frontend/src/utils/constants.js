// Constants
export const APP_NAME = 'Olly Hans Distribution'

// States
export const PRODUCT_STATES = {
  PENDING: 'pending',
  VALIDATED: 'validated',
  REJECTED: 'rejected',
}

export const PRODUCT_STATE_LABELS = {
  [PRODUCT_STATES.PENDING]: 'En attente',
  [PRODUCT_STATES.VALIDATED]: 'Validé',
  [PRODUCT_STATES.REJECTED]: 'Rejeté',
}

export const PRODUCT_STATE_COLORS = {
  [PRODUCT_STATES.PENDING]: 'warning',
  [PRODUCT_STATES.VALIDATED]: 'success',
  [PRODUCT_STATES.REJECTED]: 'danger',
}

// Pagination
export const DEFAULT_PAGE_SIZE = 20
export const PAGE_SIZE_OPTIONS = [10, 20, 50, 100]

// Sort options
export const SORT_OPTIONS = [
  { value: 'recent', label: 'Plus récents' },
  { value: 'price_asc', label: 'Prix croissant' },
  { value: 'price_desc', label: 'Prix décroissant' },
  { value: 'views', label: 'Plus vus' },
]

// Stars calculation based on views
export const calculateStars = (views) => {
  if (views >= 500) return 5
  if (views >= 400) return 4
  if (views >= 300) return 3
  if (views >= 200) return 2
  return 1
}

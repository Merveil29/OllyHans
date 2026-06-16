// Email validation
export const isValidEmail = (email) => {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return regex.test(email)
}

// Phone validation
export const isValidPhone = (phone) => {
  const regex = /^[+]?[(]?[0-9]{3}[)]?[-\s.]?[0-9]{3}[-\s.]?[0-9]{4,6}$/
  return regex.test(phone)
}

// Password strength
export const getPasswordStrength = (password) => {
  if (!password) return { score: 0, label: 'Aucun' }
  
  let score = 0
  
  // Length
  if (password.length >= 8) score++
  if (password.length >= 12) score++
  
  // Has lowercase
  if (/[a-z]/.test(password)) score++
  
  // Has uppercase
  if (/[A-Z]/.test(password)) score++
  
  // Has numbers
  if (/\d/.test(password)) score++
  
  // Has special chars
  if (/[^A-Za-z0-9]/.test(password)) score++
  
  const labels = {
    0: 'Très faible',
    1: 'Faible',
    2: 'Moyen',
    3: 'Bon',
    4: 'Fort',
    5: 'Très fort',
    6: 'Excellent',
  }
  
  return { score, label: labels[score] || 'Aucun' }
}

// Required field
export const required = (value) => {
  if (Array.isArray(value)) return value.length > 0
  return !!value
}

// Min length
export const minLength = (value, min) => {
  if (!value) return true
  return value.length >= min
}

// Max length
export const maxLength = (value, max) => {
  if (!value) return true
  return value.length <= max
}

// Min value
export const minValue = (value, min) => {
  return Number(value) >= min
}

// Max value
export const maxValue = (value, max) => {
  return Number(value) <= max
}

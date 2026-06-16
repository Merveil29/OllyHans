import axios from 'axios'
import router from '@/router'

const axiosInstance = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api/v1',
  timeout: 15000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

// Request interceptor
axiosInstance.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor
axiosInstance.interceptors.response.use(
  (response) => {
    return response.data
  },
  (error) => {
    if (error.response) {
      const { status, data } = error.response
      
      switch (status) {
        case 401:
          // Session expirée
          localStorage.removeItem('token')
          localStorage.removeItem('user')
          router.push('/login')
          break
        case 403:
          console.error('Accès refusé')
          break
        case 404:
          console.error('Ressource non trouvée')
          break
        case 422:
          // Erreurs de validation - gérées par le composant
          break
        case 429:
          console.error('Trop de requêtes. Veuillez réessayer plus tard.')
          break
        case 500:
          console.error('Erreur serveur')
          break
      }
    } else if (error.request) {
      console.error('Impossible de contacter le serveur')
    }
    
    return Promise.reject(error)
  }
)

export default axiosInstance

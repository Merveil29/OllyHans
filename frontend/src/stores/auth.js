import { defineStore } from 'pinia'
import { authAPI } from '@/api/modules/auth'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
    userType: localStorage.getItem('userType') || null, // 'client' ou 'admin'
    isAuthenticated: false,
    isAdmin: false,
    loading: false,
  }),

  getters: {
    fullName: (state) => {
      return state.user
        ? `${state.user.prenom || state.user.client_prenom || ''} ${state.user.nom || state.user.client_nom || ''}`.trim()
        : ''
    },

    tokenBalance: (state) => {
      return state.user?.jettons || 0
    },

  },

  actions: {
    async login(credentials) {
      this.loading = true
      try {
        // Préparer les données pour l'API Laravel
        const loginData = {
          identifier: credentials.identifier, // Laravel attend 'identifier' (email ou login)
          password: credentials.password,
          remember: credentials.remember || false
        }

        const response = await authAPI.login(loginData)

        // L'interceptor axios retourne déjà response.data
        // Vérifier la structure de la réponse pour connexion normale
        if (response && response.data) {
          this.setAuth(response.data)
          return { success: true, data: response }
        } else if (response && response.token) {
          // Si la réponse est directement { token, client }
          this.setAuth(response)
          return { success: true, data: response }
        }

        throw new Error('Format de réponse inattendu')
      } catch (error) {
        // Relancer l'erreur telle quelle pour que Login.vue puisse la gérer
        throw error
      } finally {
        this.loading = false
      }
    },

    async register(data) {
      this.loading = true
      try {
        const response = await authAPI.register(data)
        // L'interceptor axios retourne déjà response.data
        return { success: true, data: response }
      } catch (error) {
        throw error
      } finally {
        this.loading = false
      }
    },

    async verifyOtp(email, otp) {
      this.loading = true
      try {
        const response = await authAPI.verifyOtp({ email, otp })
        // L'interceptor axios retourne déjà response.data
        this.setAuth(response.data)
        return { success: true, data: response }
      } catch (error) {
        console.error(' Store: OTP verification error:', error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async checkEmail(email) {
      try {
        return await authAPI.checkEmail(email)
      } catch (error) {
        throw error
      }
    },

    async checkLogin(login) {
      try {
        return await authAPI.checkLogin(login)
      } catch (error) {
        throw error
      }
    },

    async fetchUser() {
      try {
        const response = await authAPI.getProfile()
        this.user = response.data
        this.isAuthenticated = true
        this.isAdmin = response.data.is_admin || false
      } catch (error) {
        this.logout()
      }
    },

    // Alias pour fetchUser
    async fetchProfile() {
      return this.fetchUser()
    },

    async logout() {
      try {
        await authAPI.logout()
      } catch (error) {
        // Continue logout même si l'API échoue
      } finally {
        this.clearAuth()
      }
    },

    async updateProfile(data) {
      this.loading = true
      try {
        const response = await authAPI.updateProfile(data)
        this.user = response.data
        return { success: true }
      } catch (error) {
        throw error
      } finally {
        this.loading = false
      }
    },

    setAuth(data) {
      this.token = data.token
      this.user = data.client || data.user
      this.userType = data.user_type || 'client' // 'client' ou 'admin'
      this.isAuthenticated = true
      this.isAdmin = this.userType === 'admin' || (data.client || data.user)?.is_admin || false
      localStorage.setItem('token', data.token)
      localStorage.setItem('user', JSON.stringify(data.client || data.user))
      localStorage.setItem('userType', this.userType)
    },

    clearAuth() {
      this.token = null
      this.user = null
      this.userType = null
      this.isAuthenticated = false
      this.isAdmin = false
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      localStorage.removeItem('userType')
    },

    // Initialize from localStorage
    initializeAuth() {
      const token = localStorage.getItem('token')
      const user = localStorage.getItem('user')
      const userType = localStorage.getItem('userType')

      if (token && user) {
        try {
          this.token = token
          this.user = JSON.parse(user)
          this.userType = userType || 'client'
          this.isAuthenticated = true
          this.isAdmin = this.userType === 'admin' || this.user.is_admin || false
        } catch (error) {
          this.clearAuth()
        }
      }
    },
  },
})

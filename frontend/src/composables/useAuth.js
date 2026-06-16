import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

export function useAuth() {
  const authStore = useAuthStore()
  const router = useRouter()
  
  const user = computed(() => authStore.user)
  const isAuthenticated = computed(() => authStore.isAuthenticated)
  const isAdmin = computed(() => authStore.isAdmin)
  const loading = computed(() => authStore.loading)
  const token = computed(() => authStore.token)
  
  const login = async (credentials) => {
    return await authStore.login(credentials)
  }
  
  const logout = async () => {
    await authStore.logout()
    router.push('/login')
  }
  
  const register = async (data) => {
    return await authStore.register(data)
  }
  
  const fetchUser = async () => {
    return await authStore.fetchUser()
  }
  
  const requireAuth = () => {
    if (!isAuthenticated.value) {
      router.push('/login')
      return false
    }
    return true
  }
  
  const requireAdmin = () => {
    if (!isAdmin.value) {
      router.push('/')
      return false
    }
    return true
  }
  
  return {
    user,
    isAuthenticated,
    isAdmin,
    loading,
    token,
    login,
    logout,
    register,
    fetchUser,
    requireAuth,
    requireAdmin,
  }
}

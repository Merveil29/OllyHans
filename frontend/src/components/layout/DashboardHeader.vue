<template>
  <header class="bg-gradient-to-r from-primary-700 via-primary-600 to-primary-500 shadow-xl sticky top-0 z-50">
    <!-- Top Bar -->
    <div class="bg-white/10 backdrop-blur-sm border-b border-white/20 relative z-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <!-- Logo -->
          <router-link to="/" class="flex items-center space-x-3">
            <img src="/images/logo-sm.png" alt="Olly Hans Distribution" class="h-10 w-auto sm:hidden" />
            <img src="/images/logo.png" alt="Olly Hans Distribution" class="hidden sm:block h-8" />
          </router-link>

          <!-- Right Section -->
          <div class="flex items-center space-x-2 sm:space-x-4">
            <!-- Jettons Pub -->
            <div class="hidden sm:flex items-center bg-white/20 backdrop-blur-md rounded-full px-3 py-1.5 border border-white/30" 
                 title="Crédits Pub">
              <svg class="w-5 h-5 text-yellow-300 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
              </svg>
              <span class="text-white font-semibold text-sm">{{ authStore.user?.jettons || 0 }}</span>
            </div>

            <!-- Jettons Sponsor -->
            <div class="hidden sm:flex items-center bg-white/20 backdrop-blur-md rounded-full px-3 py-1.5 border border-white/30" 
                 title="Crédits Sponsor">
              <svg class="w-5 h-5 text-green-300 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
              </svg>
              <span class="text-white font-semibold text-sm">{{ authStore.user?.jettons_sponsor || 0 }}</span>
            </div>

            <!-- Welcome Message (Desktop) -->
            <div class="hidden lg:block text-white text-sm px-3">
              <span class="font-medium">{{ authStore.user?.prenom || 'Bienvenue' }}</span>, ravi de te revoir !
            </div>

            <!-- User Dropdown -->
            <div class="relative" v-if="authStore.isAuthenticated">
              <button
                @click="toggleUserMenu"
                class="flex items-center space-x-2 bg-white/20 backdrop-blur-md rounded-full px-3 py-2 border border-white/30 hover:bg-white/30 transition-all"
              >
                <img
                  :src="userAvatar"
                  alt="Avatar"
                  class="w-8 h-8 rounded-full border-2 border-white object-cover"
                />
                <!-- <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg> -->
              </button>

              <!-- Dropdown Menu -->
              <transition name="fade-slide">
                <div
                  v-if="userMenuOpen"
                  class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden z-[9999]"
                  @click="userMenuOpen = false"
                >
                  <div class="px-4 py-3 bg-gradient-to-r from-primary-50 to-primary-100 border-b border-gray-200">
                    <p class="text-sm font-semibold text-gray-900">{{ authStore.user?.nom }} {{ authStore.user?.prenom }}</p>
                    <p class="text-xs text-gray-600 truncate">{{ authStore.user?.email }}</p>
                  </div>
                  
                  <div class="py-2">
                    <router-link
                      to="/profile"
                      class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition-colors"
                    >
                      <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                      Mon Profil
                    </router-link>
                    
                    <!-- <router-link
                      to="/orders"
                      class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors"
                    >
                      <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                      </svg>
                      Mes Commandes
                    </router-link> -->
                  </div>

                  <div class="border-t border-gray-200">
                    <button
                      @click="handleLogout"
                      class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"
                    >
                      <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                      </svg>
                      Déconnexion
                    </button>
                  </div>
                </div>
              </transition>
            </div>

            <!-- Mobile Menu Button -->
            <button
              @click="toggleMobileMenu"
              class="lg:hidden flex items-center justify-center w-10 h-10 rounded-lg bg-white/20 backdrop-blur-md border border-white/30 hover:bg-white/30 transition-all"
            >
              <svg v-if="!mobileMenuOpen" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
              <svg v-else class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Navigation Menu (Desktop) -->
    <nav class="hidden lg:block bg-white/5 backdrop-blur-sm border-b border-white/10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-12">
          <div class="flex items-center space-x-1">
            <router-link
              to="/dashboard"
              class="flex items-center px-4 py-2 text-sm font-medium text-white hover:bg-white/10 rounded-lg transition-all"
              active-class="bg-white/20"
            >
              <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
              </svg>
              Tableau de bord
            </router-link>

            <router-link
              to="/"
              class="flex items-center px-4 py-2 text-sm font-medium text-white hover:bg-white/10 rounded-lg transition-all"
            >
              <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
              Retour au site
            </router-link>

            <div class="relative group">
              <button class="flex items-center px-4 py-2 text-sm font-medium text-white hover:bg-white/10 rounded-lg transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Publier
                <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <div class="absolute left-0 mt-1 w-48 bg-white rounded-lg shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                <router-link to="/publish/produit" class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-700 first:rounded-t-lg">
                  Un Produit
                </router-link>
              </div>
            </div>

            <div class="relative group">
              <button class="flex items-center px-4 py-2 text-sm font-medium text-white hover:bg-white/10 rounded-lg transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Suivi
                <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <div class="absolute left-0 mt-1 w-48 bg-white rounded-lg shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                <router-link to="/suivi/produits" class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-700 first:rounded-t-lg">
                  des Produits
                </router-link>

              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Mobile Menu -->
    <transition name="slide-down">
      <div v-if="mobileMenuOpen" class="lg:hidden bg-white/10 backdrop-blur-lg border-t border-white/20">
        <div class="px-4 py-4 space-y-2">
          <!-- Mobile Jettons -->
          <div class="flex items-center justify-around mb-4 pb-4 border-b border-white/20">
            <div class="flex items-center bg-white/20 rounded-full px-3 py-1.5">
              <svg class="w-5 h-5 text-yellow-300 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
              </svg>
              <span class="text-white font-semibold text-sm">{{ authStore.user?.jettons || 0 }}</span>
              <span class="text-white/80 text-xs ml-1">Pub</span>
            </div>
            <div class="flex items-center bg-white/20 rounded-full px-3 py-1.5">
              <svg class="w-5 h-5 text-green-300 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
              </svg>
              <span class="text-white font-semibold text-sm">{{ authStore.user?.jettons_sponsor || 0 }}</span>
              <span class="text-white/80 text-xs ml-1">Sponsor</span>
            </div>
          </div>

          <router-link
            to="/dashboard"
            class="block px-4 py-2 text-white hover:bg-white/10 rounded-lg transition-all"
            @click="closeMobileMenu"
          >
            Tableau de bord
          </router-link>

          <router-link
            to="/"
            class="block px-4 py-2 text-white hover:bg-white/10 rounded-lg transition-all"
            @click="closeMobileMenu"
          >
            Retour au site
          </router-link>

          <div>
            <button
              @click="toggleMobilePublish"
              class="w-full flex items-center justify-between px-4 py-2 text-white hover:bg-white/10 rounded-lg transition-all"
            >
              Publier
              <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': mobilePublishOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <div v-if="mobilePublishOpen" class="ml-4 mt-1 space-y-1">
              <router-link to="/publish/produit" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-lg" @click="closeMobileMenu">
                Un Produit
              </router-link>
            </div>
          </div>

          <div>
            <button
              @click="toggleMobileSuivi"
              class="w-full flex items-center justify-between px-4 py-2 text-white hover:bg-white/10 rounded-lg transition-all"
            >
              Suivi
              <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': mobileSuiviOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <div v-if="mobileSuiviOpen" class="ml-4 mt-1 space-y-1">
              <router-link to="/suivi/produits" class="block px-4 py-2 text-sm text-white/80 hover:text-white hover:bg-white/10 rounded-lg" @click="closeMobileMenu">
                des Produits
              </router-link>

            </div>
          </div>
        </div>
      </div>
    </transition>
  </header>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const mobileMenuOpen = ref(false)
const userMenuOpen = ref(false)
const mobilePublishOpen = ref(false)
const mobileSuiviOpen = ref(false)

const userAvatar = computed(() => {
  const user = authStore.user
  if (!user) return '/avatar.png'
  // Support mapped field (image), client field (image_client) and admin field (image_user)
  const raw = user.image || user.image_client || user.image_user
  // No photo or default placeholder stored in DB
  if (!raw || raw === 'public/avatar.png' || raw === 'avatar.png') return '/avatar.png'
  if (raw.startsWith('http')) return raw
  // Laravel storage path: strip leading slash and 'public/' prefix
  const path = raw.replace(/^\//, '').replace(/^public\//, '')
  const base = (import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api/v1').replace('/api/v1', '')
  return `${base}/storage/${path}`
})

const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value
  if (!mobileMenuOpen.value) {
    mobilePublishOpen.value = false
    mobileSuiviOpen.value = false
  }
}

const closeMobileMenu = () => {
  mobileMenuOpen.value = false
  mobilePublishOpen.value = false
  mobileSuiviOpen.value = false
}

const toggleUserMenu = () => {
  userMenuOpen.value = !userMenuOpen.value
}

const toggleMobilePublish = () => {
  mobilePublishOpen.value = !mobilePublishOpen.value
}

const toggleMobileSuivi = () => {
  mobileSuiviOpen.value = !mobileSuiviOpen.value
}

const handleLogout = async () => {
  try {
    await authStore.logout()
    userMenuOpen.value = false
    router.push('/login')
  } catch (error) {
    console.error('Erreur lors de la déconnexion:', error)
  }
}

// Rafraîchir les données utilisateur au montage
onMounted(async () => {
  await authStore.fetchUser()
})

// Close dropdowns when clicking outside
if (typeof window !== 'undefined') {
  window.addEventListener('click', (e) => {
    if (!e.target.closest('.relative')) {
      userMenuOpen.value = false
    }
  })
}
</script>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
}

.slide-down-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}

.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.2s ease;
}

.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(-8px);
}

.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>

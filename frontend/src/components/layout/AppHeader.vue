<template>
  <header class="bg-[#000000] shadow-md sticky top-0 z-50 border-b border-white/10">
    <!-- Main Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
      <div class="flex items-center justify-between gap-8">
        <!-- Logo -->
        <router-link to="/" class="flex items-center flex-shrink-0">
          <img src="/images/logo.png" alt="Olly Hans Distribution" class="h-10 sm:h-12 w-auto" />
        </router-link>

        <!-- Desktop Navigation -->
        <nav class="hidden lg:flex items-center space-x-1 flex-1 justify-center">
          <router-link 
            to="/" 
            class="text-white/80 hover:text-primary-400 font-medium transition-colors px-3 py-2 rounded-lg hover:bg-white/5 text-sm"
            active-class="text-primary-400 bg-white/5"
          >
            Accueil
          </router-link>
          
          <div class="relative group">
            <button class="text-white/80 hover:text-primary-400 font-medium transition-colors px-3 py-2 rounded-lg hover:bg-white/5 flex items-center text-sm">
              Catégories
              <svg class="w-3 h-3 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <div class="absolute left-0 mt-2 w-48 bg-[#1a1a1a] rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 border border-white/10">
              <div class="py-2">
                <router-link 
                  v-for="category in categories" 
                  :key="category.id"
                  :to="{ path: '/products', query: { category: category.id } }"
                  class="block px-4 py-2.5 text-sm text-white/70 hover:text-primary-400 hover:bg-white/5 transition-colors"
                >
                  {{ category.nom }}
                </router-link>
              </div>
            </div>
          </div>

          <router-link 
            to="/products" 
            class="text-white/80 hover:text-primary-400 font-medium transition-colors px-3 py-2 rounded-lg hover:bg-white/5 text-sm"
            active-class="text-primary-400 bg-white/5"
          >
            Shopping
          </router-link>
          
          <router-link 
            to="/about" 
            class="text-white/80 hover:text-primary-400 font-medium transition-colors px-3 py-2 rounded-lg hover:bg-white/5 text-sm"
            active-class="text-primary-400 bg-white/5"
          >
            À propos
          </router-link>
          
          <router-link 
            to="/help" 
            class="text-white/80 hover:text-primary-400 font-medium transition-colors px-3 py-2 rounded-lg hover:bg-white/5 text-sm"
            active-class="text-primary-400 bg-white/5"
          >
            Aide
          </router-link>
        </nav>

        <!-- Actions -->
        <div class="flex items-center space-x-3 flex-shrink-0">
          <!-- Search Bar -->
          <div class="hidden md:flex items-center">
            <transition name="search-expand">
              <div v-if="searchOpen" class="flex items-center bg-white/10 rounded-full px-4 py-2 mr-2 border border-white/20">
                <input 
                  v-model="searchQuery"
                  type="text" 
                  placeholder="Rechercher..." 
                  class="bg-transparent outline-none w-48 text-sm text-white placeholder-white/40"
                  @keyup.enter="performSearch"
                  @blur="closeSearchIfEmpty"
                  ref="searchInput"
                />
              </div>
            </transition>
            <button 
              @click="toggleSearch"
              class="text-white/70 hover:text-primary-400 transition-colors p-2"
              aria-label="Rechercher"
            >
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </button>
          </div>

          <!-- Cart Button -->
          <button
            @click="showCart = true"
            class="relative text-white/70 hover:text-primary-400 transition-colors p-2"
            aria-label="Panier"
          >
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span
              v-if="cartStore.totalItems > 0"
              class="absolute -top-1 -right-1 bg-primary-400 text-black text-xs font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1"
            >
              {{ cartStore.totalItems > 99 ? '99+' : cartStore.totalItems }}
            </span>
          </button>

          <!-- Auth Buttons / User Menu -->
          <template v-if="!authStore.isAuthenticated">
            <router-link 
              to="/login" 
              class="hidden md:inline-flex items-center px-4 py-2 text-white/80 hover:text-primary-400 font-medium transition-colors text-sm"
            >
              Connexion
            </router-link>
            <router-link 
              to="/register" 
              class="hidden md:inline-flex items-center px-5 py-2 bg-primary-400 text-black font-semibold rounded-lg hover:bg-primary-500 transition-all duration-200 text-sm shadow-lg shadow-primary-400/20"
            >
              Inscription
            </router-link>
          </template>
          <div v-else class="hidden md:block relative group">
            <button class="flex items-center space-x-2 text-white/80 hover:text-primary-400 transition-colors px-3 py-2 rounded-lg hover:bg-white/5">
              <img 
                :src="userAvatar" 
                :alt="authStore.user?.nom || 'Photo de profil'" 
                class="w-8 h-8 rounded-full object-cover border-2 border-white/20"
              />
              <span class="text-sm font-medium">{{ authStore.user?.nom || 'Mon compte' }}</span>
            </button>
            <div class="absolute right-0 mt-2 w-48 bg-[#1a1a1a] rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 border border-white/10">
              <div class="py-2">
                <router-link :to="profileRoute" class="block px-4 py-2.5 text-sm text-white/70 hover:text-primary-400 hover:bg-white/5 transition-colors">
                  Mon Profil
                </router-link>
                <button 
                  @click="handleLogout"
                  class="w-full text-left px-4 py-2.5 text-sm text-red-400 hover:bg-white/5 transition-colors"
                >
                  Déconnexion
                </button>
              </div>
            </div>
          </div>

          <!-- Mobile Menu Button -->
          <button 
            @click="toggleMobileMenu"
            class="lg:hidden text-white/70 hover:text-primary-400 transition-colors p-2"
            aria-label="Menu"
          >
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Mobile Menu -->
      <transition name="slide-down">
        <nav v-if="mobileMenuOpen" class="lg:hidden mt-3 pb-4 border-t border-white/10 pt-4">
          <div class="space-y-1">
            <router-link 
              to="/" 
              class="block py-2.5 px-3 text-white/80 hover:text-primary-400 hover:bg-white/5 font-medium transition-colors rounded-lg text-sm"
              @click="closeMobileMenu"
            >
              Accueil
            </router-link>
            
            <div>
              <button 
                @click="toggleMobileCategories"
                class="w-full flex items-center justify-between py-2.5 px-3 text-white/80 hover:text-primary-400 hover:bg-white/5 font-medium transition-colors rounded-lg text-sm"
              >
                Catégories
                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': mobileCategoriesOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <div v-if="mobileCategoriesOpen" class="pl-4 space-y-1 mt-1">
                <router-link 
                  v-for="category in categories" 
                  :key="category.id"
                  :to="{ path: '/products', query: { category: category.id } }"
                  class="block py-2 px-3 text-sm text-white/60 hover:text-primary-400 rounded-lg transition-colors"
                  @click="closeMobileMenu"
                >
                  {{ category.nom }}
                </router-link>
              </div>
            </div>

            <router-link 
              to="/products" 
              class="block py-2.5 px-3 text-white/80 hover:text-primary-400 hover:bg-white/5 font-medium transition-colors rounded-lg text-sm"
              @click="closeMobileMenu"
            >
              Shopping
            </router-link>
            
            <router-link 
              to="/about" 
              class="block py-2.5 px-3 text-white/80 hover:text-primary-400 hover:bg-white/5 font-medium transition-colors rounded-lg text-sm"
              @click="closeMobileMenu"
            >
              À propos
            </router-link>
            
            <router-link 
              to="/help" 
              class="block py-2.5 px-3 text-white/80 hover:text-primary-400 hover:bg-white/5 font-medium transition-colors rounded-lg text-sm"
              @click="closeMobileMenu"
            >
              Aide
            </router-link>

            <!-- Mobile Auth -->
            <div class="border-t border-white/10 pt-3 mt-3">
              <template v-if="!authStore.isAuthenticated">
                <router-link 
                  to="/login" 
                  class="block py-2.5 px-3 text-white/80 hover:text-primary-400 hover:bg-white/5 font-medium rounded-lg transition-colors text-sm"
                  @click="closeMobileMenu"
                >
                  Connexion
                </router-link>
                <router-link 
                  to="/register" 
                  class="block py-2.5 px-3 text-primary-400 font-semibold rounded-lg transition-colors text-sm"
                  @click="closeMobileMenu"
                >
                  Inscription
                </router-link>
              </template>
              <template v-else>
                <router-link 
                  :to="profileRoute" 
                  class="block py-2.5 px-3 text-white/80 hover:text-primary-400 rounded-lg transition-colors text-sm"
                  @click="closeMobileMenu"
                >
                  Mon Profil
                </router-link>
                <button 
                  @click="handleLogout"
                  class="w-full text-left py-2.5 px-3 text-red-400 hover:bg-white/5 rounded-lg transition-colors text-sm"
                >
                  Déconnexion
                </button>
              </template>
            </div>
          </div>
        </nav>
      </transition>
    </div>

    <CartDrawer v-model="showCart" whatsappNumber="+221771234567" />
  </header>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import { categoriesAPI } from '@/api/modules/categories'
import CartDrawer from '@/components/cart/CartDrawer.vue'

const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()

// Image de profil de l'utilisateur
const userAvatar = computed(() => {
  const image = authStore.user?.image || authStore.user?.image_client || ''
  if (!image) return '/avatar.png'
  if (image.startsWith('http')) return image
  return `/${image}`
})

// Route du profil selon le rôle (admin ou client)
const profileRoute = computed(() => {
  return authStore.isAdmin ? '/admin/profile' : '/profile'
})

const mobileMenuOpen = ref(false)
const mobileCategoriesOpen = ref(false)
const searchOpen = ref(false)
const searchQuery = ref('')
const searchInput = ref(null)
const showCart = ref(false)

// Catégories chargées dynamiquement
const categories = ref([])
const loadingCategories = ref(false)

// Charger les catégories depuis l'API
const loadCategories = async () => {
  try {
    loadingCategories.value = true
    const response = await categoriesAPI.getAll()
    if (response.success) {
      categories.value = response.data
    }
  } catch (error) {
    console.error('Erreur lors du chargement des catégories:', error)
  } finally {
    loadingCategories.value = false
  }
}

const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value
  if (!mobileMenuOpen.value) {
    mobileCategoriesOpen.value = false
  }
}

const closeMobileMenu = () => {
  mobileMenuOpen.value = false
  mobileCategoriesOpen.value = false
}

const toggleMobileCategories = () => {
  mobileCategoriesOpen.value = !mobileCategoriesOpen.value
}

const toggleSearch = async () => {
  searchOpen.value = !searchOpen.value
  if (searchOpen.value) {
    await nextTick()
    searchInput.value?.focus()
  } else {
    searchQuery.value = ''
  }
}

const closeSearchIfEmpty = () => {
  setTimeout(() => {
    if (!searchQuery.value.trim()) {
      searchOpen.value = false
    }
  }, 200)
}

const performSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ path: '/search', query: { q: searchQuery.value } })
    searchOpen.value = false
    searchQuery.value = ''
  }
}

const handleLogout = async () => {
  try {
    await authStore.logout()
    closeMobileMenu()
    router.push('/login')
  } catch (error) {
    console.error('Erreur lors de la déconnexion:', error)
  }
}

// Fermer le menu mobile lors du changement de route
watch(() => router.currentRoute.value, () => {
  closeMobileMenu()
})

// Charger les catégories au montage du composant
onMounted(() => {
  loadCategories()
})
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

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.search-expand-enter-active,
.search-expand-leave-active {
  transition: all 0.3s ease;
}

.search-expand-enter-from,
.search-expand-leave-to {
  opacity: 0;
  transform: scaleX(0);
  transform-origin: right;
}
</style>

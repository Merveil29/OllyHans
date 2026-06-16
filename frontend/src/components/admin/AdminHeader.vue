<template>
  <header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
    <div class="px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <!-- Logo -->
        <div class="flex items-center">
          <router-link to="/admin/dashboard" class="flex items-center">
            <img src="/images/logo-sm.png" alt="Olly Hans Distribution" class="h-10 w-auto" />
          </router-link>
        </div>

        <!-- Search Bar (Desktop) -->
        <div class="hidden md:flex flex-1 max-w-md mx-8">
          <div class="relative w-full">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Rechercher..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              @keyup.enter="handleSearch"
            />
            <svg
              class="absolute left-3 top-2.5 w-5 h-5 text-gray-400"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
        </div>

        <!-- Right Section -->
        <div class="flex items-center space-x-2">
          <!-- Notifications -->
          <div class="relative" ref="notifDropdown">
            <button
              @click="toggleNotifications"
              class="relative p-2 text-gray-600 hover:text-primary-600 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
              <span
                v-if="notificationsCount > 0"
                class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-error-500 rounded-full"
              >
                {{ notificationsCount }}
              </span>
            </button>

            <!-- Notifications Dropdown -->
            <transition name="dropdown">
              <div
                v-if="showNotifications"
                class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden"
              >
                <div class="p-4 bg-gray-50 border-b border-gray-200">
                  <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                </div>
                <div class="max-h-96 overflow-y-auto">
                  <router-link
                    v-for="notification in notifications"
                    :key="notification.id"
                    :to="notification.link"
                    class="block p-4 hover:bg-gray-50 border-b border-gray-100 transition-colors"
                    @click="showNotifications = false"
                  >
                    <div class="flex items-start space-x-3">
                      <div
                        class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center"
                        :class="getNotificationBgClass(notification.type)"
                      >
                        <svg class="w-5 h-5" :class="getNotificationIconClass(notification.type)" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getNotificationIcon(notification.type)" />
                        </svg>
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">{{ notification.title }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ notification.message }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ notification.time }}</p>
                      </div>
                    </div>
                  </router-link>
                  <div v-if="notifications.length === 0" class="p-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p class="text-sm">Aucune notification</p>
                  </div>
                </div>
                <router-link
                  to="/admin/notifications"
                  class="block p-3 text-center text-sm font-medium text-primary-600 hover:bg-primary-50 transition-colors"
                  @click="showNotifications = false"
                >
                  Voir toutes les notifications
                </router-link>
              </div>
            </transition>
          </div>

          <!-- User Menu -->
          <div class="relative" ref="userDropdown">
            <button
              @click="toggleUserMenu"
              class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 transition-colors"
            >
              <img
                :src="user?.image || '/images/avatar-default.png'"
                alt="Admin"
                class="w-8 h-8 rounded-full border-2 border-primary-200"
              />
              <div class="hidden md:block text-left">
                <p class="text-sm font-medium text-gray-900">{{ user?.prenom }} {{ user?.nom }}</p>
                <p class="text-xs text-gray-500">Administrateur</p>
              </div>
              <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- User Dropdown -->
            <transition name="dropdown">
              <div
                v-if="showUserMenu"
                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden"
              >
                <router-link
                  to="/admin/profile"
                  class="flex items-center space-x-2 px-4 py-3 hover:bg-gray-50 transition-colors"
                  @click="showUserMenu = false"
                >
                  <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  <span class="text-sm text-gray-700">Mon Profil</span>
                </router-link>
                <router-link
                  to="/"
                  class="flex items-center space-x-2 px-4 py-3 hover:bg-gray-50 transition-colors"
                  @click="showUserMenu = false"
                >
                  <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                  </svg>
                  <span class="text-sm text-gray-700">Page d'accueil</span>
                </router-link>
                <button
                  @click="handleLogout"
                  class="w-full flex items-center space-x-2 px-4 py-3 hover:bg-error-50 transition-colors text-left"
                >
                  <svg class="w-5 h-5 text-error-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                  </svg>
                  <span class="text-sm text-error-600 font-medium">Déconnexion</span>
                </button>
              </div>
            </transition>
          </div>

          <!-- Mobile menu button -->
          <button
            @click="$emit('toggle-sidebar')"
            class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors"
          >
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { getNotificationStats } from '@/api/modules/admin/notifications'

defineEmits(['toggle-sidebar'])

const router = useRouter()
const authStore = useAuthStore()

const searchQuery = ref('')
const showNotifications = ref(false)
const showUserMenu = ref(false)

const notifDropdown = ref(null)
const userDropdown = ref(null)

const user = computed(() => authStore.user)

const notifications = ref([])
const notificationsCount = ref(0)

const loadNotifications = async () => {
  try {
    const statsRes = await getNotificationStats()

    if (statsRes?.data) {
      const s = statsRes.data
      notificationsCount.value = s.total ?? 0

      const newNotifs = []
      if ((s.products ?? 0) > 0) {
        newNotifs.push({
          id: 'products',
          type: 'product',
          title: 'Annonces en attente',
          message: `${s.products} annonce(s) en attente de validation`,
          time: 'À traiter',
          link: '/admin/products'
        })
      }
      notifications.value = newNotifs
    }
  } catch (e) {
    console.error('Erreur chargement notifications header:', e)
  }
}

const toggleNotifications = () => {
  showNotifications.value = !showNotifications.value
  showUserMenu.value = false
}

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
  showNotifications.value = false
}

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ name: 'AdminSearch', query: { q: searchQuery.value } })
  }
}

const handleLogout = async () => {
  try {
    await authStore.logout()
    router.push('/login')
  } catch (error) {
    console.error('Erreur lors de la déconnexion:', error)
  }
}

const getNotificationBgClass = (type) => {
  const classes = {
    product: 'bg-primary-100',
    user: 'bg-success-100'
  }
  return classes[type] || 'bg-gray-100'
}

const getNotificationIconClass = (type) => {
  const classes = {
    product: 'text-primary-600',
    user: 'text-success-600'
  }
  return classes[type] || 'text-gray-600'
}

const getNotificationIcon = (type) => {
  const icons = {
    product: 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4',
    user: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'
  }
  return icons[type] || 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
}

const handleClickOutside = (event) => {
  if (notifDropdown.value && !notifDropdown.value.contains(event.target)) {
    showNotifications.value = false
  }
  if (userDropdown.value && !userDropdown.value.contains(event.target)) {
    showUserMenu.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  loadNotifications()
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>

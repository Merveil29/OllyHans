<template>
  <div>
    <DashboardHeader />

    <div class="max-w-6xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-8">
      <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
          <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Tableau de bord</h1>
          <p class="text-sm text-gray-600 mt-1">
            Bienvenue, {{ displayName }}. Voici un resume de votre espace client.
          </p>
        </div>
        <div class="flex items-center gap-3">
          <router-link to="/publish/produit"
            class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
            Publier un produit
          </router-link>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <router-link to="/suivi/produits"
          class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Mes produits</p>
              <p class="mt-2 text-sm text-gray-600">Suivre vos annonces publiees</p>
            </div>
            <div class="p-3 bg-primary-50 rounded-lg">
              <svg class="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
            </div>
          </div>
        </router-link>

        <router-link to="/profile"
          class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Mon Profil</p>
              <p class="mt-2 text-sm text-gray-600">Gerer vos informations personnelles</p>
            </div>
            <div class="p-3 bg-blue-50 rounded-lg">
              <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
          </div>
        </router-link>
      </div>

      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h2 class="text-sm font-semibold text-gray-900">Vos statistiques</h2>
            <p class="text-xs text-gray-500">Produits publies</p>
          </div>
          <button @click="loadStats" :disabled="statsLoading"
            class="text-xs text-gray-600 hover:text-gray-900 disabled:opacity-50">
            {{ statsLoading ? 'Chargement...' : 'Actualiser' }}
          </button>
        </div>

        <div v-if="statsLoading" class="h-20 rounded-lg bg-gray-100 animate-pulse"></div>
        <div v-else>
          <div class="rounded-lg border border-gray-200 p-4 inline-block">
            <p class="text-xs text-gray-500 uppercase tracking-wider">Produits</p>
            <p class="mt-2 text-2xl font-bold text-primary-600">{{ stats.products }}</p>
          </div>
        </div>

        <div v-if="statsError" class="mt-3 text-xs text-red-600">{{ statsError }}</div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
          <h2 class="text-sm font-semibold text-gray-900 mb-3">Actions rapides</h2>
          <div class="flex flex-wrap gap-3">
            <router-link to="/publish/produit"
              class="inline-flex items-center gap-2 px-3 py-2 text-sm text-primary-700 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors">
              Publier un produit
            </router-link>
            <router-link to="/profile"
              class="inline-flex items-center gap-2 px-3 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
              Modifier mon profil
            </router-link>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
          <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-semibold text-gray-900">Mes credits</h2>
          </div>
          <div class="flex items-center gap-6">
            <div>
              <p class="text-xs text-gray-500 uppercase tracking-wider">Jetons pub</p>
              <p class="text-2xl font-bold text-gray-900">{{ authStore.user?.jettons || 0 }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { productsAPI } from '@/api/modules/products'
import DashboardHeader from '@/components/layout/DashboardHeader.vue'

const authStore = useAuthStore()

const stats = ref({ products: 0 })
const statsLoading = ref(false)
const statsError = ref('')

const displayName = computed(() => {
  const first = authStore.user?.prenom || authStore.user?.client_prenom || ''
  const last = authStore.user?.nom || authStore.user?.client_nom || ''
  const full = `${first} ${last}`.trim()
  return full || 'client'
})

const loadStats = async () => {
  statsLoading.value = true
  statsError.value = ''
  try {
    const [productsRes] = await Promise.allSettled([
      productsAPI.getMyProducts()
    ])

    const productsData = productsRes.status === 'fulfilled' ? productsRes.value : null
    const productsList = productsData?.data?.produits || productsData?.data || []

    stats.value = {
      products: Array.isArray(productsList) ? productsList.length : 0,
    }
  } catch {
    statsError.value = 'Impossible de charger les statistiques.'
  } finally {
    statsLoading.value = false
  }
}

onMounted(loadStats)
</script>

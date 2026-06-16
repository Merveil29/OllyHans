<template>
  <div>
    <!-- Page Title -->
    <div class="mb-8 flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Tableau de Bord</h1>
        <p class="text-gray-600 mt-1">Vue d'ensemble de la plateforme Olly Hans Distribution</p>
      </div>
      <button @click="loadDashboard" :disabled="loading"
        class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors">
        <svg :class="['w-4 h-4', loading && 'animate-spin']" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        Actualiser
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div v-for="n in 8" :key="n" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 animate-pulse">
        <div class="h-4 bg-gray-200 rounded w-3/4 mb-3"></div>
        <div class="h-8 bg-gray-200 rounded w-1/2"></div>
      </div>
    </div>

    <template v-else>
      <!-- Annonces / Produits -->
      <div class="mb-6">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Annonces</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <router-link to="/admin/products?filter=pending" class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">En attente</p>
            <p class="mt-2 text-3xl font-bold text-warning-600">{{ stats.produits.en_attente }}</p>
            <p class="mt-1 text-xs text-gray-400">sur {{ stats.produits.total }} au total</p>
          </router-link>
          <router-link to="/admin/products?filter=validated" class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Publiées</p>
            <p class="mt-2 text-3xl font-bold text-success-600">{{ stats.produits.publies }}</p>
          </router-link>
          <router-link to="/admin/products?filter=rejected" class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Refusées</p>
            <p class="mt-2 text-3xl font-bold text-error-600">{{ stats.produits.refuses }}</p>
          </router-link>
          <router-link to="/admin/products" class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ stats.produits.total }}</p>
          </router-link>
        </div>
      </div>

      <!-- Clients -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        <!-- Clients -->
        <router-link to="/admin/clients" class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Clients</p>
              <p class="mt-2 text-3xl font-bold text-gray-900">{{ stats.clients.total }}</p>
            </div>
            <div class="p-3 bg-indigo-50 rounded-lg">
              <svg class="w-7 h-7 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
          </div>
        </router-link>

        <!-- Admins -->
        <router-link to="/admin/users" class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Administrateurs</p>
              <p class="mt-2 text-3xl font-bold text-gray-900">-</p>
            </div>
            <div class="p-3 bg-green-50 rounded-lg">
              <svg class="w-7 h-7 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
          </div>
        </router-link>
      </div>
    </template>

    <!-- Error -->
    <div v-if="error" class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-700">
      {{ error }}
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { getDashboard } from '@/api/modules/admin/dashboard'

const loading = ref(true)
const error = ref('')

const stats = ref({
  clients: { total: 0 },
  produits: { total: 0, en_attente: 0, publies: 0, refuses: 0 },
})

const loadDashboard = async () => {
  loading.value = true
  error.value = ''
  try {
    const response = await getDashboard()
    if (response.success && response.data) {
      stats.value = response.data
    }
  } catch (e) {
    error.value = 'Erreur lors du chargement du tableau de bord.'
    console.error(e)
  } finally {
    loading.value = false
  }
}

onMounted(loadDashboard)


</script>

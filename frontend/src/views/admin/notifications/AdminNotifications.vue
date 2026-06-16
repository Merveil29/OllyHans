<template>
  <div class="p-6 space-y-6">

    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
        <p class="text-sm text-gray-500 mt-1">Éléments nécessitant votre attention</p>
      </div>
      <button
        @click="refresh"
        :disabled="loading"
        class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 disabled:opacity-50 transition-colors"
      >
        <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        Actualiser
      </button>
    </div>

    <!-- Cartes de statistiques -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
          <svg class="w-6 h-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
          </svg>
        </div>
        <div>
          <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
          <p class="text-xs text-gray-500">Total en attente</p>
        </div>
      </div>

      <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
          <svg class="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
          </svg>
        </div>
        <div>
          <p class="text-2xl font-bold text-primary-600">{{ stats.products }}</p>
          <p class="text-xs text-gray-500">Annonces</p>
        </div>
      </div>

    </div>

    <!-- Filtres / onglets -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <div class="flex border-b border-gray-200 overflow-x-auto">
        <button
          v-for="tab in tabs"
          :key="tab.value"
          @click="setFilter(tab.value)"
          class="flex-shrink-0 flex items-center gap-2 px-5 py-3 text-sm font-medium transition-colors border-b-2 -mb-px"
          :class="activeFilter === tab.value
            ? 'border-primary-600 text-primary-600 bg-primary-50'
            : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
        >
          <span>{{ tab.label }}</span>
          <span
            v-if="tab.count !== undefined"
            class="px-1.5 py-0.5 rounded-full text-xs font-bold"
            :class="activeFilter === tab.value ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-600'"
          >{{ tab.count }}</span>
        </button>
      </div>

      <!-- État de chargement -->
      <div v-if="loading" class="py-16 text-center text-gray-400">
        <svg class="animate-spin w-8 h-8 mx-auto mb-3 text-primary-400" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
        </svg>
        <p class="text-sm">Chargement…</p>
      </div>

      <!-- Vide -->
      <div v-else-if="!loading && notifications.length === 0" class="py-20 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-gray-500 font-medium">Aucune notification</p>
        <p class="text-gray-400 text-sm mt-1">Tout est à jour !</p>
      </div>

      <!-- Liste -->
      <ul v-else class="divide-y divide-gray-100">
        <li
          v-for="notif in notifications"
          :key="notif.id"
          class="flex items-start gap-4 px-5 py-4 hover:bg-gray-50 transition-colors group"
        >
          <!-- Icône -->
          <div
            class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mt-0.5"
            :class="iconBg(notif.type)"
          >
            <svg class="w-5 h-5" :class="iconColor(notif.type)" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="iconPath(notif.type)" />
            </svg>
          </div>

          <!-- Contenu -->
          <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2">
              <div>
                <p class="text-sm font-semibold text-gray-900">{{ notif.title }}</p>
                <p class="text-sm text-gray-600 mt-0.5 truncate max-w-lg">{{ notif.message }}</p>
                <p v-if="notif.sub" class="text-xs text-gray-400 mt-0.5">{{ notif.sub }}</p>
              </div>
              <div class="flex-shrink-0 flex flex-col items-end gap-1">
                <span class="text-xs text-gray-400 whitespace-nowrap">
                  {{ formatDate(notif.date) }}
                </span>
                <span
                  class="text-xs px-2 py-0.5 rounded-full font-medium"
                  :class="priorityClass(notif.priority)"
                >
                  {{ priorityLabel(notif.priority) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Action -->
          <router-link
            :to="notif.link"
            class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity self-center"
            title="Voir"
          >
            <span class="inline-flex items-center gap-1 text-xs text-primary-600 hover:text-primary-800 font-medium px-3 py-1.5 border border-primary-200 rounded-lg hover:bg-primary-50 transition-colors">
              Voir
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </span>
          </router-link>
        </li>
      </ul>

      <!-- Pagination -->
      <div
        v-if="pagination.last_page > 1"
        class="flex items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50"
      >
        <p class="text-sm text-gray-500">
          Page {{ pagination.current_page }} / {{ pagination.last_page }}
          <span class="ml-2 text-gray-400">({{ pagination.total }} éléments)</span>
        </p>
        <div class="flex gap-2">
          <button
            @click="goPage(pagination.current_page - 1)"
            :disabled="pagination.current_page <= 1"
            class="px-3 py-1.5 text-sm border border-gray-200 rounded-lg disabled:opacity-40 hover:bg-white transition-colors"
          >Précédent</button>
          <button
            @click="goPage(pagination.current_page + 1)"
            :disabled="pagination.current_page >= pagination.last_page"
            class="px-3 py-1.5 text-sm border border-gray-200 rounded-lg disabled:opacity-40 hover:bg-white transition-colors"
          >Suivant</button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { getNotifications, getNotificationStats } from '@/api/modules/admin/notifications'

// ── État ────────────────────────────────────────────────────────────────────
const loading       = ref(false)
const notifications = ref([])
const activeFilter  = ref('all')
const pagination    = ref({ total: 0, per_page: 20, current_page: 1, last_page: 1 })
const stats         = ref({ total: 0, products: 0 })

// ── Onglets ─────────────────────────────────────────────────────────────────
const tabs = computed(() => [
  { value: 'all',     label: 'Tout',      count: stats.value.total    },
  { value: 'product', label: 'Annonces',  count: stats.value.products },
])

// ── Chargement ──────────────────────────────────────────────────────────────
const load = async (page = 1) => {
  loading.value = true
  try {
    const [notifRes, statsRes] = await Promise.allSettled([
      getNotifications({ type: activeFilter.value, page, per_page: 20 }),
      getNotificationStats(),
    ])

    if (notifRes.status === 'fulfilled') {
      const res = notifRes.value
      notifications.value = Array.isArray(res.data) ? res.data : (res.data ?? [])
      if (res.pagination) pagination.value = res.pagination
      else if (res.meta)  pagination.value = res.meta
    }

    if (statsRes.status === 'fulfilled') {
      stats.value = statsRes.value?.data ?? stats.value
    }
  } catch (e) {
    console.error('Erreur notifications:', e)
  } finally {
    loading.value = false
  }
}

const refresh    = () => load(1)
const setFilter  = (f) => { activeFilter.value = f; load(1) }
const goPage     = (p) => load(p)

onMounted(refresh)

// ── Helpers visuels ─────────────────────────────────────────────────────────
const iconBg = (type) => ({
  product: 'bg-primary-100',
}[type] ?? 'bg-gray-100')

const iconColor = (type) => ({
  product: 'text-primary-600',
}[type] ?? 'text-gray-500')

const iconPath = (type) => ({
  product: 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4',
}[type] ?? 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z')

const priorityClass = (p) => ({
  high:   'bg-red-100 text-red-700',
  medium: 'bg-orange-100 text-orange-700',
  low:    'bg-gray-100 text-gray-600',
}[p] ?? 'bg-gray-100 text-gray-600')

const priorityLabel = (p) => ({
  high:   'Urgent',
  medium: 'À traiter',
  low:    'Info',
}[p] ?? p)

const formatDate = (d) => {
  if (!d) return '—'
  try {
    return new Intl.DateTimeFormat('fr-FR', {
      day: '2-digit', month: 'short', year: 'numeric',
    }).format(new Date(d))
  } catch {
    return d
  }
}
</script>

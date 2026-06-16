<template>
  <div class="min-h-screen bg-gray-50">
    <DashboardHeader />
    <div class="py-8 px-4 space-y-6">
      <!-- En-tête avec statistiques -->
      <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Mes Produits</h1>
            <p class="text-gray-600 mt-1">Gérez vos annonces de produits</p>
          </div>
          
          <!-- Bouton Créer -->
          <router-link
            to="/publish/produit"
            class="inline-flex items-center gap-2 bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 transition-colors duration-200 font-semibold shadow-sm"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nouveau Produit
          </router-link>
        </div>
      </div>

      <!-- Filtres et recherche -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Recherche -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Rechercher par nom ou description..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              />
            </div>
          </div>

          <!-- Filtre par état -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">État</label>
            <select
              v-model="selectedFilter"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
            >
              <option value="all">Tous les états</option>
              <option value="1">En attente</option>
              <option value="2">Validés</option>
              <option value="3">Refusés</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Statistiques rapides -->
      <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600">Total</p>
              <p class="text-2xl font-bold text-primary-600">{{ products.length }}</p>
            </div>
            <div class="p-3 bg-primary-100 rounded-lg">
              <svg class="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600">En attente</p>
              <p class="text-2xl font-bold text-warning-600">{{ products.filter(p => p.state?.id_state === 1).length }}</p>
            </div>
            <div class="p-3 bg-warning-100 rounded-lg">
              <svg class="w-6 h-6 text-warning-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600">Validés</p>
              <p class="text-2xl font-bold text-success-600">{{ products.filter(p => p.state?.id_state === 2).length }}</p>
            </div>
            <div class="p-3 bg-success-100 rounded-lg">
              <svg class="w-6 h-6 text-success-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600">Refusés</p>
              <p class="text-2xl font-bold text-error-600">{{ products.filter(p => p.state?.id_state === 3).length }}</p>
            </div>
            <div class="p-3 bg-error-100 rounded-lg">
              <svg class="w-6 h-6 text-error-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Liste des produits -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- Loading -->
        <div v-if="loading" class="p-12 text-center">
          <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-primary-200 border-t-primary-600"></div>
          <p class="mt-4 text-gray-600">Chargement des produits...</p>
        </div>

        <!-- Empty state -->
        <div v-else-if="!filteredProducts.length" class="p-12 text-center">
          <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun produit trouvé</h3>
          <p class="text-gray-500 mb-4">{{ selectedFilter === 'all' ? 'Vous n\'avez pas encore créé de produit' : 'Aucun produit ne correspond à vos critères' }}</p>
          <router-link
            v-if="selectedFilter === 'all'"
            to="/publish/produit"
            class="inline-flex items-center gap-2 bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 transition-colors duration-200 font-semibold"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Créer mon premier produit
          </router-link>
        </div>

        <!-- Table responsive -->
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Produit
                </th>
                <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Prix
                </th>
                <th class="hidden lg:table-cell px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Catégorie
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  État
                </th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="product in paginatedProducts" :key="product.id_produits" class="hover:bg-gray-50">
                <!-- Produit info -->
                <td class="px-4 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-12 w-12">
                      <img
                        v-if="product.images?.main || product.image_produits"
                        :src="getProductImage(product.images?.main || product.image_produits)"
                        :alt="product.nom_produits"
                        class="h-12 w-12 rounded-lg object-cover border border-gray-200"
                        @error="handleImageError"
                      />
                      <div
                        v-else
                        class="h-12 w-12 rounded-lg bg-gradient-to-br from-primary-400 to-secondary-400 flex items-center justify-center"
                      >
                        <span class="text-white font-semibold text-lg">
                          {{ product.nom_produits.charAt(0).toUpperCase() }}
                        </span>
                      </div>
                    </div>
                    <div class="ml-3">
                      <p class="text-sm font-medium text-gray-900 line-clamp-1">{{ product.nom_produits }}</p>
                      <p class="text-xs text-gray-500 line-clamp-1 md:hidden">{{ formatPrice(product.prix_produits) }} FCFA</p>
                    </div>
                  </div>
                </td>

                <!-- Prix (hidden on mobile) -->
                <td class="hidden md:table-cell px-4 py-4 whitespace-nowrap">
                  <div class="text-sm font-semibold text-primary-600">{{ formatPrice(product.prix_produits) }} FCFA</div>
                </td>

                <!-- Catégorie (hidden on tablet) -->
                <td class="hidden lg:table-cell px-4 py-4">
                  <div class="text-sm">
                    <p class="text-gray-900">{{ getCategoryName(product) }}</p>
                    <p class="text-gray-500 text-xs">{{ getSubCategoryName(product) }}</p>
                  </div>
                </td>

                <!-- État -->
                <td class="px-4 py-4 whitespace-nowrap">
                  <span
                    :class="getStateBadgeClass(product.state?.id_state)"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  >
                    {{ {'En Instance':'En attente','Publier':'Publié','Refuser':'Refusé'}[product.state?.title] ?? product.state?.title ?? 'En attente' }}
                  </span>
                </td>

                <!-- Actions -->
                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex items-center justify-end gap-2">
                    <!-- View -->
                    <button
                      @click="handleView(product)"
                      class="p-2 text-primary-600 hover:bg-primary-50 rounded-lg transition-colors"
                      title="Voir détails"
                    >
                      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </button>

                    <!-- Edit (only if not validated) -->
                    <button
                      v-if="product.state?.id_state !== 2"
                      @click="editProduct(product)"
                      class="p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors"
                      title="Modifier"
                    >
                      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </button>

                    <!-- Delete -->
                    <button
                      @click="confirmDelete(product)"
                      class="p-2 text-error-600 hover:bg-error-50 rounded-lg transition-colors"
                      title="Supprimer"
                    >
                      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Pagination -->
          <div v-if="totalPages > 1" class="px-4 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-sm text-gray-600">
              {{ (currentPage - 1) * perPage + 1 }}–{{ Math.min(currentPage * perPage, filteredProducts.length) }}
              sur {{ filteredProducts.length }} produit{{ filteredProducts.length > 1 ? 's' : '' }}
            </p>
            <div class="flex items-center gap-1">
              <button
                @click="currentPage = 1"
                :disabled="currentPage === 1"
                class="p-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed"
              >
                «
              </button>
              <button
                @click="currentPage--"
                :disabled="currentPage === 1"
                class="p-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed"
              >
                ‹
              </button>
              <template v-for="page in totalPages" :key="page">
                <button
                  v-if="page === 1 || page === totalPages || Math.abs(page - currentPage) <= 1"
                  @click="currentPage = page"
                  :class="[
                    'w-9 h-9 rounded-lg border text-sm font-medium',
                    page === currentPage
                      ? 'bg-primary-600 text-white border-primary-600'
                      : 'border-gray-300 text-gray-700 hover:bg-gray-50'
                  ]"
                >
                  {{ page }}
                </button>
                <span
                  v-else-if="page === currentPage - 2 || page === currentPage + 2"
                  class="px-1 text-gray-400"
                >…</span>
              </template>
              <button
                @click="currentPage++"
                :disabled="currentPage === totalPages"
                class="p-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed"
              >
                ›
              </button>
              <button
                @click="currentPage = totalPages"
                :disabled="currentPage === totalPages"
                class="p-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed"
              >
                »
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <Teleport to="body">
      <div v-if="showDeleteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50" @click.self="showDeleteModal = false">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Confirmer la suppression</h3>
          <p class="text-gray-600 mb-6">
            Êtes-vous sûr de vouloir supprimer le produit <strong>{{ productToDelete?.nom_produits }}</strong> ? Cette action est irréversible.
          </p>
          <div class="flex gap-3 justify-end">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 font-medium"
            >
              Annuler
            </button>
            <button
              @click="deleteProduct"
              :disabled="deleting"
              class="px-4 py-2 bg-error-600 text-white rounded-lg hover:bg-error-700 transition-colors duration-200 font-medium disabled:opacity-50"
            >
              <span v-if="deleting">Suppression...</span>
              <span v-else>Supprimer</span>
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Modal de détails -->
    <ProductDetailModal
      :show="showDetailModal"
      :product="selectedProduct || {}"
      @close="showDetailModal = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { productsAPI } from '@/api/modules/products'
import DashboardHeader from '@/components/layout/DashboardHeader.vue'
import ProductDetailModal from '@/components/client/products/ProductDetailModal.vue'

const router = useRouter()
const authStore = useAuthStore()

const products = ref([])
const loading = ref(true)
const selectedFilter = ref('all')
const searchQuery = ref('')
const showDeleteModal = ref(false)
const productToDelete = ref(null)
const deleting = ref(false)
const showDetailModal = ref(false)
const selectedProduct = ref(null)

// Pagination
const currentPage = ref(1)
const perPage = ref(10)

const filteredProducts = computed(() => {
  let filtered = products.value

  // Filtre par état
  if (selectedFilter.value !== 'all') {
    filtered = filtered.filter(p => p.state?.id_state === parseInt(selectedFilter.value))
  }

  // Filtre par recherche
  if (searchQuery.value.trim()) {
    const search = searchQuery.value.toLowerCase()
    filtered = filtered.filter(p =>
      p.nom_produits?.toLowerCase().includes(search) ||
      p.description_produits?.toLowerCase().includes(search)
    )
  }

  return filtered
})

// Reset pagination when filter/search changes
watch([selectedFilter, searchQuery], () => { currentPage.value = 1 })

const totalPages = computed(() => Math.ceil(filteredProducts.value.length / perPage.value) || 1)

const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * perPage.value
  return filteredProducts.value.slice(start, start + perPage.value)
})

const getCategoryName = (product) => {
  return (
    product?.sous_categorie?.categorie?.nom_categorie ||
    product?.categorie?.nom_categorie ||
    product?.categorie ||
    '-'
  )
}

const getSubCategoryName = (product) => {
  return (
    product?.sous_categorie?.libelle_sous_categorie ||
    product?.sous_categorie ||
    '-'
  )
}

const loadProducts = async () => {
  loading.value = true
  try {
    const response = await productsAPI.getMyProducts()
    if (response.success && response.data) {
      // response.data = { produits: [...], jetons_restants: N }
      products.value = Array.isArray(response.data) ? response.data : (response.data.produits || [])
    }
  } catch (error) {
    console.error('Erreur lors du chargement des produits:', error)
  } finally {
    loading.value = false
  }
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR').format(price)
}

const handleView = (product) => {
  selectedProduct.value = product
  showDetailModal.value = true
}

const editProduct = (product) => {
  router.push(`/suivi/produits/${product.id_produits}/edit`)
}

const confirmDelete = (product) => {
  productToDelete.value = product
  showDeleteModal.value = true
}

const deleteProduct = async () => {
  if (!productToDelete.value) return
  
  deleting.value = true
  try {
    await productsAPI.deleteProduct(productToDelete.value.id_produits)
    products.value = products.value.filter(p => p.id_produits !== productToDelete.value.id_produits)
    showDeleteModal.value = false
    productToDelete.value = null
    
    // Rafraîchir les jetons
    await authStore.fetchProfile()
  } catch (error) {
    console.error('Erreur lors de la suppression:', error)
    alert('Une erreur est survenue lors de la suppression')
  } finally {
    deleting.value = false
  }
}

const getStateBadgeClass = (stateId) => {
  switch (stateId) {
    case 1: return 'bg-warning-100 text-warning-800'
    case 2: return 'bg-success-100 text-success-800'
    case 3: return 'bg-error-100 text-error-800'
    default: return 'bg-gray-100 text-gray-800'
  }
}



const getProductImage = (imagePath) => {
  if (!imagePath) return ''
  if (imagePath.startsWith('http')) return imagePath
  return `${import.meta.env.VITE_API_URL}/storage/${imagePath}`
}

const handleImageError = (event) => {
  event.target.src = '/images/placeholder-product.png'
}

onMounted(() => {
  loadProducts()
})
</script>

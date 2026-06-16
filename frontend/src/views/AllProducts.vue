<template>
  <AppLayout>
    <div class="min-h-screen bg-gray-50 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ pageTitle }}</h1>
          <p class="text-gray-600">{{ pageDescription }}</p>
        </div>

        <!-- Filtres et recherche -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
          <div class="flex flex-col md:flex-row gap-4">
            <!-- Barre de recherche -->
            <div class="flex-1 relative">
              <div class="relative">
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Rechercher un produit..."
                  class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                  @input="handleSearch"
                  @focus="showSuggestions = true"
                  @blur="hideSuggestions"
                />
                <svg
                  class="absolute left-3 top-2.5 h-5 w-5 text-gray-400"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                  />
                </svg>
                
                <!-- Bouton pour effacer la recherche -->
                <button
                  v-if="searchQuery"
                  @click="clearSearch"
                  class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600"
                >
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
              
              <!-- Suggestions -->
              <div
                v-if="showSuggestions && suggestions.length > 0"
                class="absolute z-10 w-full mt-2 bg-white rounded-lg shadow-lg border border-gray-200 max-h-96 overflow-y-auto"
              >
                <div
                  v-for="suggestion in suggestions"
                  :key="suggestion.id_produits"
                  @mousedown="selectSuggestion(suggestion)"
                  class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0"
                >
                  <img
                    :src="suggestion.image_produits || '/images/default-product.png'"
                    :alt="suggestion.nom_produits"
                    class="w-12 h-12 object-cover rounded"
                    @error="handleImageError"
                  />
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">
                      {{ suggestion.nom_produits }}
                    </p>
                    <p class="text-sm text-primary-600 font-semibold">
                      {{ formatPrice(suggestion.prix_produits) }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Tri -->
            <div class="w-full md:w-64">
              <select
                v-model="sortBy"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                @change="loadProducts"
              >
                <option value="recent">Plus récents</option>
                <option value="price_asc">Prix croissant</option>
                <option value="price_desc">Prix décroissant</option>
                <option value="name">Nom A-Z</option>
              </select>
            </div>
          </div>

          <!-- Résultats -->
          <div v-if="!loading && products.length > 0" class="mt-3 text-sm text-gray-600">
            Affichage de {{ pagination.from }} à {{ pagination.to }} sur {{ pagination.total }} produit{{ pagination.total > 1 ? 's' : '' }}
          </div>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-4 border-primary-200 border-t-primary-600"></div>
        </div>

        <!-- Grille de produits -->
        <div v-else-if="products.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
          <div
            v-for="product in filteredProducts"
            :key="product.id_produits"
            class="group bg-white rounded-lg sm:rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-primary-300 transform hover:-translate-y-1 cursor-pointer"
            @click="openProductDetail(product)"
          >
            <!-- Image -->
            <div class="relative h-40 sm:h-48 bg-gradient-to-br from-primary-50 to-secondary-50 overflow-hidden flex items-center justify-center">
              <img
                :src="product.image_produits"
                :alt="product.nom_produits"
                class="max-w-full max-h-full object-contain p-2 sm:p-4 group-hover:scale-110 transition-transform duration-300"
                @error="handleImageError"
              />
              
              <!-- Badge Prix -->
              <div class="absolute top-3 right-3">
                <span class="bg-white/95 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-bold text-primary-600 shadow-sm">
                  {{ formatPrice(product.prix_produits) }}
                </span>
              </div>

              <!-- Overlay au hover -->
              <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </div>

            <!-- Informations -->
            <div class="p-4 sm:p-6">
              <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-2 group-hover:text-primary-600 transition-colors line-clamp-1">
                {{ product.nom_produits }}
              </h3>
              
              <!-- Catégorie -->
              <p v-if="product.sous_categorie" class="text-xs sm:text-sm text-gray-600 mb-3 sm:mb-4 line-clamp-1">
                {{ product.sous_categorie.categorie?.nom_categorie }}
                <span v-if="product.sous_categorie.nom_sous_categorie"> / {{ product.sous_categorie.nom_sous_categorie }}</span>
              </p>

              <!-- Actions -->
              <div class="flex items-center justify-between text-xs sm:text-sm">
                <span class="text-gray-500">
                  {{ formatPrice(product.prix_produits) }}
                </span>
                <div class="flex items-center gap-2">
                  <button
                    @click.stop="addToCart(product)"
                    class="px-3 py-1.5 bg-primary-600 text-white text-xs font-semibold rounded-lg hover:bg-primary-700 transition-colors flex items-center gap-1"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Ajouter
                  </button>
                  <span class="text-primary-600 font-semibold flex items-center gap-1 group-hover:gap-2 transition-all">
                    Voir détails
                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- État vide -->
        <div v-else class="bg-white rounded-lg shadow-sm p-12 text-center">
          <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun produit trouvé</h3>
          <p class="text-gray-500">
            {{ searchQuery ? 'Aucun produit ne correspond à votre recherche' : 'Aucun produit disponible pour le moment' }}
          </p>
        </div>

        <!-- Pagination -->
        <div v-if="!loading && pagination.last_page > 1" class="flex justify-center items-center gap-2 mt-8">
          <!-- Bouton Précédent -->
          <button
            :disabled="pagination.current_page === 1"
            class="px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            @click="goToPage(pagination.current_page - 1)"
          >
            Précédent
          </button>

          <!-- Numéros de page -->
          <div class="flex gap-2">
            <button
              v-for="page in displayedPages"
              :key="page"
              :class="[
                'px-4 py-2 rounded-lg font-medium transition-colors',
                page === pagination.current_page
                  ? 'bg-primary-600 text-white'
                  : 'bg-white border border-gray-300 hover:bg-gray-50'
              ]"
              @click="goToPage(page)"
            >
              {{ page }}
            </button>
          </div>

          <!-- Bouton Suivant -->
          <button
            :disabled="pagination.current_page === pagination.last_page"
            class="px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            @click="goToPage(pagination.current_page + 1)"
          >
            Suivant
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { productsAPI } from '@/api/modules/products'
import { categoriesAPI } from '@/api/modules/categories'
import { useCartStore } from '@/stores/cart'
import AppLayout from '@/components/layout/AppLayout.vue'

const router = useRouter()
const route = useRoute()
const cartStore = useCartStore()

// État
const products = ref([])
const loading = ref(false)
const searchQuery = ref('')
const sortBy = ref('recent')
const categoryId = ref(null)
const categoryName = ref('')
const suggestions = ref([])
const showSuggestions = ref(false)
const pagination = ref({
  total: 0,
  per_page: 12,
  current_page: 1,
  last_page: 1,
  from: 0,
  to: 0
})

// Titre de la page
const pageTitle = computed(() => {
  return categoryName.value ? `Produits - ${categoryName.value}` : 'Shopping'
})

const pageDescription = computed(() => {
  return categoryName.value 
    ? `Découvrez tous nos produits de la catégorie ${categoryName.value}` 
    : 'Découvrez tous nos produits validés'
})

// Produits filtrés
const filteredProducts = computed(() => {
  let filtered = [...products.value]

  // Tri
  switch (sortBy.value) {
    case 'price_asc':
      filtered.sort((a, b) => parseFloat(a.prix_produits) - parseFloat(b.prix_produits))
      break
    case 'price_desc':
      filtered.sort((a, b) => parseFloat(b.prix_produits) - parseFloat(a.prix_produits))
      break
    case 'name':
      filtered.sort((a, b) => a.nom_produits.localeCompare(b.nom_produits))
      break
    default: // recent
      // Déjà trié par date décroissante depuis l'API
      break
  }

  return filtered
})

// Pages affichées pour la pagination
const displayedPages = computed(() => {
  const pages = []
  const current = pagination.value.current_page
  const last = pagination.value.last_page
  
  // Afficher au maximum 5 pages
  let start = Math.max(1, current - 2)
  let end = Math.min(last, current + 2)
  
  // Ajuster si on est au début ou à la fin
  if (current <= 3) {
    end = Math.min(5, last)
  }
  if (current >= last - 2) {
    start = Math.max(1, last - 4)
  }
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

// Charger les produits
const loadProducts = async (page = 1) => {
  loading.value = true
  try {
    let response
    
    const params = {
      page,
      per_page: 12,
      search: searchQuery.value.trim()
    }
    
    // Si une catégorie est sélectionnée, utiliser l'API des catégories
    if (categoryId.value) {
      response = await categoriesAPI.getProducts(categoryId.value, params)
    } else {
      // Sinon, charger tous les produits
      response = await productsAPI.getPublicProducts(params)
    }
    
    if (response.success) {
      products.value = response.data
      pagination.value = response.pagination
    }
  } catch (error) {
    console.error('Erreur lors du chargement des produits:', error)
  } finally {
    loading.value = false
  }
}

// Recherche
let searchTimeout = null
let suggestionsTimeout = null

const handleSearch = () => {
  // Charger les suggestions
  clearTimeout(suggestionsTimeout)
  if (searchQuery.value.trim().length >= 2) {
    suggestionsTimeout = setTimeout(() => {
      loadSuggestions()
    }, 200)
  } else {
    suggestions.value = []
  }
  
  // Charger les produits
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    loadProducts(1) // Retour à la page 1 lors de la recherche
  }, 500)
}

// Charger les suggestions
const loadSuggestions = async () => {
  if (searchQuery.value.trim().length < 2) {
    suggestions.value = []
    return
  }
  
  try {
    const response = await productsAPI.getSuggestions(searchQuery.value.trim())
    if (response.success) {
      suggestions.value = response.data
    }
  } catch (error) {
    console.error('Erreur lors du chargement des suggestions:', error)
    suggestions.value = []
  }
}

// Sélectionner une suggestion
const selectSuggestion = (suggestion) => {
  showSuggestions.value = false
  openProductDetail(suggestion)
}

// Masquer les suggestions
const hideSuggestions = () => {
  setTimeout(() => {
    showSuggestions.value = false
  }, 200)
}

// Effacer la recherche
const clearSearch = () => {
  searchQuery.value = ''
  suggestions.value = []
  showSuggestions.value = false
  loadProducts(1)
}

// Navigation pagination
const goToPage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    loadProducts(page)
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

// Navigation vers détails produit
const openProductDetail = (product) => {
  router.push({ name: 'product-detail', params: { id: product.id_produits } })
}

// Ajouter au panier
const addToCart = (product) => {
  cartStore.addItem(product)
}

// Formatage prix
const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'XAF',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(price)
}

// Gestion erreur image
const handleImageError = (event) => {
  event.target.src = '/images/default-product.png'
}

// Charger les informations de la catégorie
const loadCategoryInfo = async () => {
  if (!categoryId.value) {
    categoryName.value = ''
    return
  }
  
  try {
    const response = await categoriesAPI.getById(categoryId.value)
    if (response.success) {
      categoryName.value = response.data.nom
    }
  } catch (error) {
    console.error('Erreur lors du chargement de la catégorie:', error)
  }
}

// Initialiser à partir des query params
const initFromRoute = async () => {
  categoryId.value = route.query.category ? parseInt(route.query.category) : null
  await loadCategoryInfo()
  loadProducts()
}

// Surveiller les changements de query params
watch(() => route.query.category, () => {
  initFromRoute()
})

// Chargement initial
onMounted(() => {
  initFromRoute()
})
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>

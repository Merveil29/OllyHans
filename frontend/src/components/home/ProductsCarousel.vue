<template>
  <section v-if="products.length > 0" class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- En-tête -->
      <div class="text-center mb-10">
        <span class="text-primary-400 font-semibold text-sm tracking-widest uppercase">Notre sélection</span>
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-3 mb-3">
          Nouveautés
        </h2>
        <p class="text-gray-500 text-lg">
          Découvrez nos derniers produits ajoutés
        </p>
      </div>

      <!-- Carrousel -->
      <div class="relative overflow-hidden">
        <!-- Container des slides -->
        <div 
          ref="carouselContainer"
          class="flex transition-transform duration-700 ease-in-out"
          :style="{ transform: `translateX(-${currentIndex * slideWidth}%)` }"
        >
          <!-- Toutes les cartes de produits -->
          <div
            v-for="product in products"
            :key="product.id_produits"
            class="group bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-500 overflow-hidden border border-gray-100 hover:border-primary-300/50 transform hover:-translate-y-1.5 cursor-pointer flex-shrink-0 px-2 sm:px-3"
            :style="{ width: cardWidth }"
            @click="openProductDetail(product)"
          >
            <!-- Image -->
            <div class="relative h-40 sm:h-48 bg-gradient-to-br from-gray-50 to-primary-50/30 overflow-hidden flex items-center justify-center">
              <img
                :src="getProductImage(product.image_produits)"
                :alt="product.nom_produits"
                class="max-w-full max-h-full object-contain p-3 sm:p-5 group-hover:scale-110 transition-transform duration-500"
                @error="handleImageError"
              />
              
              <!-- Badge Prix -->
              <div class="absolute top-3 right-3">
                <span class="bg-black/80 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-bold text-primary-400 shadow-lg">
                  {{ formatPrice(product.prix_produits) }}
                </span>
              </div>
            </div>

            <!-- Informations -->
            <div class="p-4 sm:p-5">
              <h3 class="text-sm sm:text-base font-bold text-gray-900 mb-1.5 group-hover:text-primary-600 transition-colors line-clamp-1">
                {{ product.nom_produits }}
              </h3>
              
              <!-- Catégorie -->
              <p v-if="product.sous_categorie" class="text-xs text-gray-400 mb-3 line-clamp-1">
                {{ product.sous_categorie.categorie?.nom_categorie }}
                <span v-if="product.sous_categorie.nom_sous_categorie"> / {{ product.sous_categorie.nom_sous_categorie }}</span>
              </p>

              <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                <span class="text-sm font-bold text-gray-900">
                  {{ formatPrice(product.prix_produits) }}
                </span>
                <span class="text-xs text-primary-600 font-semibold flex items-center gap-1 group-hover:gap-2 transition-all">
                  Voir détails
                  <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Boutons de navigation -->
        <button
          v-if="maxIndex > 0"
          @click="previousSlide"
          :disabled="currentIndex === 0"
          :class="{ 'opacity-50 cursor-not-allowed': currentIndex === 0 }"
          class="absolute left-0 top-1/2 -translate-y-1/2 bg-black/70 hover:bg-black text-white p-3 rounded-full shadow-lg hover:shadow-xl transition-all duration-200 z-10 hidden sm:block backdrop-blur-sm"
          aria-label="Produit précédent"
        >
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>

        <button
          v-if="maxIndex > 0"
          @click="nextSlide"
          :disabled="currentIndex === maxIndex"
          :class="{ 'opacity-50 cursor-not-allowed': currentIndex === maxIndex }"
          class="absolute right-0 top-1/2 -translate-y-1/2 bg-black/70 hover:bg-black text-white p-3 rounded-full shadow-lg hover:shadow-xl transition-all duration-200 z-10 hidden sm:block backdrop-blur-sm"
          aria-label="Produit suivant"
        >
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>

        <!-- Indicateurs -->
        <div v-if="maxIndex > 0" class="flex justify-center gap-2 mt-8">
          <button
            v-for="index in maxIndex + 1"
            :key="`indicator-${index - 1}`"
            @click="goToSlide(index - 1)"
            class="transition-all duration-300"
            :class="[
              currentIndex === index - 1
                ? 'w-8 bg-primary-400' 
                : 'w-2 bg-gray-200 hover:bg-gray-300'
            ]"
            style="height: 8px; border-radius: 4px;"
            :aria-label="`Aller à la position ${index}`"
          />
        </div>
      </div>

      <!-- Bouton voir tous les produits -->
      <div class="text-center mt-10">
        <router-link
          to="/products"
          class="inline-flex items-center gap-2 bg-black text-white px-8 py-3.5 rounded-lg hover:bg-gray-800 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl group border border-white/10"
        >
          Voir tous les produits
          <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
          </svg>
        </router-link>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { productsAPI } from '@/api/modules/products'

const router = useRouter()
const products = ref([])
const currentIndex = ref(0)
const carouselContainer = ref(null)
const itemsPerSlide = ref(4) // Par défaut: desktop
let autoplayInterval = null

// Détecter la taille de l'écran et ajuster itemsPerSlide
const updateItemsPerSlide = () => {
  const width = window.innerWidth
  if (width < 640) {
    // Mobile
    itemsPerSlide.value = 1
  } else if (width < 1024) {
    // Tablette
    itemsPerSlide.value = 2
  } else {
    // Desktop
    itemsPerSlide.value = 4
  }
  
  // Réinitialiser l'index si nécessaire
  if (currentIndex.value > maxIndex.value) {
    currentIndex.value = maxIndex.value
  }
}

// Calculer la largeur de chaque carte en pourcentage
const cardWidth = computed(() => {
  return `${100 / itemsPerSlide.value}%`
})

// Calculer la largeur du slide (pour translateX)
const slideWidth = computed(() => {
  return 100 / itemsPerSlide.value
})

// Index maximum pour le carousel (nombre de slides possibles)
const maxIndex = computed(() => {
  return Math.max(0, products.value.length - itemsPerSlide.value)
})

// Charger les produits récents
const loadProducts = async () => {
  try {
    const response = await productsAPI.getPublicProducts({
      sort: 'recent',
      per_page: 12
    })
    
    if (response.success && response.data) {
      products.value = response.data
      
      // Démarrer l'autoplay seulement s'il y a plusieurs slides possibles
      if (maxIndex.value > 0) {
        startAutoplay()
      }
    }
  } catch (error) {
    console.error('Erreur lors du chargement des produits:', error)
  }
}

// Navigation
const nextSlide = () => {
  if (currentIndex.value < maxIndex.value) {
    currentIndex.value++
  } else {
    currentIndex.value = 0 // Retour au début
  }
}

const previousSlide = () => {
  if (currentIndex.value > 0) {
    currentIndex.value--
  } else {
    currentIndex.value = maxIndex.value // Aller à la fin
  }
}

const goToSlide = (index) => {
  currentIndex.value = index
}

// Autoplay
const startAutoplay = () => {
  autoplayInterval = setInterval(() => {
    nextSlide()
  }, 5000) // Change toutes les 5 secondes
}

const stopAutoplay = () => {
  if (autoplayInterval) {
    clearInterval(autoplayInterval)
  }
}

// Obtenir l'URL de l'image du produit
const getProductImage = (imagePath) => {
  if (!imagePath) return '/images/default-product.png'
  if (imagePath.startsWith('http')) return imagePath
  return imagePath
}

// Gestion erreur image
const handleImageError = (event) => {
  event.target.src = '/images/default-product.png'
}

// Formatage prix
const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'XAF',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(price || 0)
}

// Navigation vers détails produit
const openProductDetail = (product) => {
  router.push({ name: 'product-detail', params: { id: product.id_produits } })
}

// Lifecycle
onMounted(() => {
  loadProducts()
  updateItemsPerSlide()
  
  // Écouter les changements de taille d'écran
  window.addEventListener('resize', updateItemsPerSlide)
})

onUnmounted(() => {
  stopAutoplay()
  window.removeEventListener('resize', updateItemsPerSlide)
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
</style>

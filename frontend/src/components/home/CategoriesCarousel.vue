<template>
  <section v-if="categories.length > 0" class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- En-tête -->
      <div class="text-center mb-8">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">
          Nos Catégories
        </h2>
        <p class="text-gray-600 text-lg">
          Parcourez nos différentes catégories de produits
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
          <!-- Toutes les cartes de catégories -->
          <router-link
            v-for="category in categories"
            :key="category.id_categorie"
            :to="{ path: '/products', query: { category: category.id_categorie } }"
            class="group bg-white rounded-lg sm:rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-primary-300 transform hover:-translate-y-1 flex-shrink-0 px-2 sm:px-3"
            :style="{ width: cardWidth }"
          >
            <!-- Image de la catégorie -->
            <div class="relative h-40 sm:h-48 bg-gradient-to-br from-primary-50 to-secondary-50 overflow-hidden flex items-center justify-center">
              <img
                v-if="category.image_categorie"
                :src="getCategoryImage(category.image_categorie)"
                :alt="category.nom_categorie"
                class="max-w-full max-h-full object-contain p-4 sm:p-6 group-hover:scale-110 transition-transform duration-300"
                @error="handleImageError"
              />
              <div v-else class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-gradient-to-br from-primary-500 to-secondary-500 flex items-center justify-center">
                <span class="text-2xl sm:text-3xl font-bold text-white">
                  {{ category.nom_categorie?.charAt(0).toUpperCase() || 'C' }}
                </span>
              </div>
              
              <!-- Overlay au hover -->
              <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </div>

            <!-- Informations -->
            <div class="p-4 sm:p-6">
              <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-2 group-hover:text-primary-600 transition-colors line-clamp-1">
                {{ category.nom_categorie }}
              </h3>
              
              <p v-if="category.description_categorie" class="text-xs sm:text-sm text-gray-600 mb-3 sm:mb-4 line-clamp-2">
                {{ category.description_categorie }}
              </p>
              
              <!-- Nombre de produits (si disponible) -->
              <div class="flex items-center justify-between text-xs sm:text-sm">
                <span class="text-gray-500">
                  {{ category.produits_count || 0 }} produit{{ (category.produits_count || 0) !== 1 ? 's' : '' }}
                </span>
                <span class="text-primary-600 font-semibold flex items-center gap-1 group-hover:gap-2 transition-all">
                  Découvrir
                  <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </span>
              </div>
            </div>
          </router-link>
        </div>

        <!-- Boutons de navigation -->
        <button
          v-if="maxIndex > 0"
          @click="previousSlide"
          :disabled="currentIndex === 0"
          :class="{ 'opacity-50 cursor-not-allowed': currentIndex === 0 }"
          class="absolute left-0 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 p-3 rounded-full shadow-lg hover:shadow-xl transition-all duration-200 z-10 hidden sm:block"
          aria-label="Catégorie précédente"
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
          class="absolute right-0 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 p-3 rounded-full shadow-lg hover:shadow-xl transition-all duration-200 z-10 hidden sm:block"
          aria-label="Catégorie suivante"
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
                ? 'w-8 bg-primary-600' 
                : 'w-2 bg-gray-300 hover:bg-gray-400'
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
          class="inline-flex items-center gap-2 bg-gradient-to-r from-primary-600 to-secondary-600 text-white px-8 py-3 rounded-lg hover:from-primary-700 hover:to-secondary-700 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl group"
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
import { categoriesAPI } from '@/api/modules/categories'

const categories = ref([])
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
  return Math.max(0, categories.value.length - itemsPerSlide.value)
})

// Charger les catégories
const loadCategories = async () => {
  try {
    const response = await categoriesAPI.getAll()
    if (response.success && response.data) {
      categories.value = response.data
      
      // Démarrer l'autoplay seulement s'il y a plusieurs slides possibles
      if (maxIndex.value > 0) {
        startAutoplay()
      }
    }
  } catch (error) {
    console.error('Erreur lors du chargement des catégories:', error)
  }
}

// Navigation
const nextSlide = () => {
  if (currentIndex.value < maxIndex.value) {
    currentIndex.value++
    resetAutoplay()
  }
}

const previousSlide = () => {
  if (currentIndex.value > 0) {
    currentIndex.value--
    resetAutoplay()
  }
}

const goToSlide = (index) => {
  currentIndex.value = Math.min(Math.max(0, index), maxIndex.value)
  resetAutoplay()
}

// Autoplay
const startAutoplay = () => {
  autoplayInterval = setInterval(() => {
    if (currentIndex.value >= maxIndex.value) {
      currentIndex.value = 0
    } else {
      currentIndex.value++
    }
  }, 6000) // Change toutes les 6 secondes
}

const stopAutoplay = () => {
  if (autoplayInterval) {
    clearInterval(autoplayInterval)
    autoplayInterval = null
  }
}

const resetAutoplay = () => {
  stopAutoplay()
  if (maxIndex.value > 0) {
    startAutoplay()
  }
}

// Gestion des erreurs d'image
const handleImageError = (event) => {
  event.target.style.display = 'none'
}

// Formater l'image de catégorie
const getCategoryImage = (imagePath) => {
  if (!imagePath) return ''
  if (imagePath.startsWith('http')) return imagePath
  return `${import.meta.env.VITE_API_URL}/storage/${imagePath}`
}

// Lifecycle
onMounted(() => {
  loadCategories()
  updateItemsPerSlide()
  window.addEventListener('resize', updateItemsPerSlide)
})

onUnmounted(() => {
  stopAutoplay()
  window.removeEventListener('resize', updateItemsPerSlide)
})
</script>

<style scoped>
/* Animation douce pour le carrousel */
.transition-transform {
  transition-property: transform;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  show: Boolean,
  product: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['close'])

const getImageUrl = (imagePath) => {
  if (!imagePath) return '/placeholder-product.png'
  if (imagePath.startsWith('http')) return imagePath
  const base = (import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api/v1').replace('/api/v1', '')
  const cleanPath = imagePath.replace(/^\//, '').replace(/^public\//, '')
  return `${base}/storage/${cleanPath}`
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR').format(price || 0)
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

const images = computed(() => {
  const imgs = []
  const src = props.product.images?.main || props.product.image_produits
  const src1 = props.product.images?.img1 || props.product.image_produits1
  const src2 = props.product.images?.img2 || props.product.image_produits2
  if (src) imgs.push(getImageUrl(src))
  if (src1) imgs.push(getImageUrl(src1))
  if (src2) imgs.push(getImageUrl(src2))
  return imgs.length > 0 ? imgs : ['/placeholder-product.png']
})

const currentImage = computed(() => images.value[0])

const getStateInfo = (state) => {
  const states = {
    'Publier': { 
      label: 'Validé', 
      class: 'bg-success-50 text-success-600 border border-success-200',
      icon: '✓'
    },
    'En Instance': { 
      label: 'En attente', 
      class: 'bg-warning-50 text-warning-600 border border-warning-200',
      icon: '⌛'
    },
    'Refuser': { 
      label: 'Rejeté', 
      class: 'bg-error-50 text-error-600 border border-error-200',
      icon: '✗'
    }
  }
  return states[state] || states['En Instance']
}
</script>

<template>
  <transition name="modal">
    <div 
      v-if="show" 
      class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4"
      @click.self="emit('close')"
    >
      <div 
        class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col"
        @click.stop
      >
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gradient-to-r from-primary-50 to-secondary-50">
          <div class="flex-1">
            <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ product.nom_produits || 'Détails du produit' }}</h2>
            <p class="text-sm text-gray-600">Référence: {{ product.id_produits || '-' }}</p>
          </div>
          <button
            @click="emit('close')"
            class="ml-4 p-2 hover:bg-white rounded-lg transition-colors"
          >
            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-6">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Images -->
            <div class="space-y-4">
              <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 border border-gray-200">
                <img
                  :src="currentImage"
                  :alt="product.nom_produits"
                  class="w-full h-full object-cover"
                  @error="$event.target.src = '/placeholder-product.png'"
                />
              </div>
              <div v-if="images.length > 1" class="grid grid-cols-3 gap-2">
                <div
                  v-for="(img, idx) in images"
                  :key="idx"
                  class="aspect-square rounded-lg overflow-hidden bg-gray-100 border-2 transition-all cursor-pointer hover:border-primary-500"
                  :class="img === currentImage ? 'border-primary-500' : 'border-gray-200'"
                >
                  <img
                    :src="img"
                    :alt="`${product.nom_produits} - ${idx + 1}`"
                    class="w-full h-full object-cover"
                    @error="$event.target.src = '/placeholder-product.png'"
                  />
                </div>
              </div>
            </div>

            <!-- Informations -->
            <div class="space-y-6">
              <!-- Prix et État -->
              <div class="bg-gradient-to-br from-primary-50 to-secondary-50 rounded-xl p-6 border border-primary-100">
                <div class="flex items-center justify-between mb-4">
                  <span class="text-sm font-medium text-gray-600">Prix</span>
                  <span
                    :class="getStateInfo(product.state?.title).class"
                    class="px-3 py-1 rounded-full text-xs font-semibold flex items-center gap-1"
                  >
                    <span>{{ getStateInfo(product.state?.title).icon }}</span>
                    {{ getStateInfo(product.state?.title).label }}
                  </span>
                </div>
                <div class="text-3xl font-bold text-primary-600">
                  {{ formatPrice(product.prix_produits) }} FCFA
                </div>
              </div>

              <!-- Description -->
              <div v-if="product.description_produits" class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                  <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                  </svg>
                  Description
                </h3>
                <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ product.description_produits }}</p>
              </div>

              <!-- Catégorie -->
              <div v-if="product.categorie" class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                  <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                  </svg>
                  Catégorie
                </h3>
                <div class="flex items-center gap-3">
                  <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-primary-500 to-secondary-500 flex items-center justify-center">
                    <span class="text-white text-lg font-bold">
                    {{ product.categorie?.charAt(0).toUpperCase() || 'C' }}
                    </span>
                  </div>
                  <div>
                    <p class="font-semibold text-gray-900">{{ product.categorie }}</p>
                    <p v-if="product.sous_categorie" class="text-sm text-gray-600">{{ product.sous_categorie }}</p>
                  </div>
                </div>
              </div>

              <!-- Informations complémentaires -->
              <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 space-y-3">
                <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                  <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  Informations
                </h3>
                
                <div class="flex items-center justify-between py-2">
                  <span class="text-sm text-gray-600">Date de création</span>
                  <span class="text-sm font-medium text-gray-900">{{ formatDate(product.dateSaisie) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="border-t border-gray-200 p-6 bg-gray-50 flex justify-end">
          <button
            @click="emit('close')"
            class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500"
          >
            Fermer
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .bg-white,
.modal-leave-active .bg-white {
  transition: transform 0.3s ease;
}

.modal-enter-from .bg-white,
.modal-leave-to .bg-white {
  transform: scale(0.95);
}
</style>

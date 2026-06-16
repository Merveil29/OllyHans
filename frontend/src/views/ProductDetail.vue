<template>
  <AppLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-4 sm:py-8">
      <!-- Loading -->
      <div v-if="loading" class="flex justify-center items-center min-h-[60vh]">
        <div class="animate-spin rounded-full h-16 w-16 border-4 border-primary-200 border-t-primary-600"></div>
      </div>

      <!-- Erreur -->
      <div v-else-if="error" class="max-w-3xl mx-auto px-4">
        <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
          <svg class="mx-auto h-12 w-12 text-red-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <h3 class="text-lg font-semibold text-red-900 mb-2">Produit non trouvé</h3>
          <p class="text-red-700 mb-4">{{ error }}</p>
          <button
            @click="$router.push('/products')"
            class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
          >
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour au shopping
          </button>
        </div>
      </div>

      <!-- Contenu du produit -->
      <div v-else-if="product" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="mb-4 sm:mb-6">
          <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li>
              <button @click="$router.push('/')" class="hover:text-primary-600 transition-colors">
                Accueil
              </button>
            </li>
            <li>
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
              </svg>
            </li>
            <li>
              <button @click="$router.push('/products')" class="hover:text-primary-600 transition-colors">
                Shopping
              </button>
            </li>
            <li>
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
              </svg>
            </li>
            <li class="text-gray-900 font-medium truncate max-w-[200px]">
              {{ product.nom_produits }}
            </li>
          </ol>
        </nav>

        <!-- Grille principale -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 mb-8">
          <!-- Galerie d'images -->
          <div class="space-y-4">
            <!-- Image principale -->
            <div class="relative bg-white rounded-2xl shadow-lg overflow-hidden group">
              <div class="aspect-[4/3] lg:aspect-square flex items-center justify-center p-6 sm:p-10 bg-gradient-to-br from-gray-50 to-gray-100">
                <img
                  :src="currentImage"
                  :alt="product.nom_produits"
                  class="w-full h-full object-contain"
                  @error="handleImageError"
                />
              </div>
              
              <!-- Badges -->
              <div class="absolute top-4 right-4 space-y-2">
                <div class="bg-white/95 backdrop-blur-sm px-4 py-2 rounded-full shadow-lg">
                  <span class="text-2xl font-bold text-primary-600">
                    {{ formatPrice(product.prix_produits) }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Miniatures -->
            <div class="grid grid-cols-3 gap-4">
              <button
                v-for="(image, index) in productImages"
                :key="index"
                @click="currentImage = image"
                :class="[
                  'relative aspect-square bg-white rounded-xl overflow-hidden border-2 transition-all duration-200',
                  currentImage === image
                    ? 'border-primary-500 shadow-lg scale-105'
                    : 'border-gray-200 hover:border-primary-300 hover:shadow-md'
                ]"
              >
                <img
                  :src="image"
                  :alt="`${product.nom_produits} - Image ${index + 1}`"
                  class="w-full h-full object-contain p-3"
                  @error="handleImageError"
                />
              </button>
            </div>
          </div>

          <!-- Informations du produit -->
          <div class="space-y-6">
            <!-- Titre et catégorie -->
            <div>
              <div v-if="product.sous_categorie" class="flex items-center gap-2 text-sm text-primary-600 font-medium mb-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                {{ product.sous_categorie.categorie?.nom_categorie }}
                <span v-if="product.sous_categorie.nom_sous_categorie" class="text-gray-400">/</span>
                <span v-if="product.sous_categorie.nom_sous_categorie">
                  {{ product.sous_categorie.nom_sous_categorie }}
                </span>
              </div>
              
              <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-3">
                {{ product.nom_produits }}
              </h1>

              <!-- Stats -->
              <div class="flex items-center gap-4 sm:gap-6 flex-wrap">
                <!-- Ranking étoiles -->
                <div class="flex items-center gap-2">
                  <div class="flex items-center">
                    <svg
                      v-for="star in 5"
                      :key="star"
                      :class="[
                        'w-5 h-5',
                        star <= productRanking ? 'text-yellow-400 fill-current' : 'text-gray-300'
                      ]"
                      viewBox="0 0 20 20"
                    >
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                  </div>
                  <span class="text-sm text-gray-600 font-medium">
                    {{ productRanking }}/5
                  </span>
                </div>

                <!-- Nombre de vues -->
                <div class="flex items-center gap-2 bg-gray-100 px-3 py-1.5 rounded-full">
                  <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  <span class="text-sm font-semibold text-gray-700">
                    {{ vuesCount }} {{ vuesCount <= 1 ? 'vue' : 'vues' }}
                  </span>
                </div>

                <!-- Date de publication -->
                <div class="flex items-center gap-2 text-sm text-gray-500">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  {{ formatDate(product.dateSaisie) }}
                </div>
              </div>
            </div>

            <!-- Prix -->
            <div class="bg-gradient-to-r from-primary-50 to-secondary-50 rounded-xl p-6 border border-primary-100">
              <div class="flex items-baseline gap-2">
                <span class="text-sm text-gray-600 font-medium">Prix :</span>
                <span class="text-4xl font-bold text-primary-600">
                  {{ formatPrice(product.prix_produits) }}
                </span>
              </div>
            </div>

            <!-- Description -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
              <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Description
              </h2>
              <p class="text-gray-700 whitespace-pre-line leading-relaxed">
                {{ product.description_produits || 'Aucune description disponible.' }}
              </p>
            </div>

            <!-- Vendeur -->
            <div v-if="product.client" class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
              <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Vendeur
              </h2>
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-primary-400 to-secondary-400 rounded-full flex items-center justify-center text-white font-bold text-lg">
                  {{ getInitials(product.client.nom_client, product.client.prenom_client) }}
                </div>
                <div>
                  <p class="font-semibold text-gray-900">
                    {{ product.client.prenom_client }} {{ product.client.nom_client }}
                  </p>
                  <p class="text-sm text-gray-500">Vendeur particulier</p>
                </div>
              </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col gap-3">
              <button
                @click="addToCart"
                class="w-full bg-white hover:bg-gray-50 text-primary-600 border-2 border-primary-600 font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-3 text-lg"
              >
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Ajouter au panier
              </button>
              <button
                @click="showCommanderModal = true"
                class="w-full bg-gradient-to-r from-primary-600 to-secondary-600 hover:from-primary-700 hover:to-secondary-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-3 text-lg"
              >
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Commander maintenant
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Commander -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="showCommanderModal"
          class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
          @click.self="showCommanderModal = false"
        >
          <Transition
            enter-active-class="transition ease-out duration-200 transform"
            enter-from-class="scale-95 opacity-0"
            enter-to-class="scale-100 opacity-100"
            leave-active-class="transition ease-in duration-150 transform"
            leave-from-class="scale-100 opacity-100"
            leave-to-class="scale-95 opacity-0"
          >
            <div
              v-if="showCommanderModal"
              class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden"
            >
              <!-- Header -->
              <div class="bg-gradient-to-r from-primary-600 to-secondary-600 px-6 py-4 flex items-center justify-between">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                  <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                  Contacter le vendeur
                </h3>
                <button
                  @click="showCommanderModal = false"
                  class="text-white/80 hover:text-white transition-colors"
                >
                  <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <!-- Body -->
              <div class="p-6 space-y-4">
                <p class="text-gray-600 text-center mb-6">
                  Choisissez votre méthode de contact préférée pour commander ce produit
                </p>

                <!-- Bouton WhatsApp -->
                <a
                  :href="whatsappUrl"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="flex items-center justify-center gap-3 w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                >
                  <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                  </svg>
                  Contacter via WhatsApp
                </a>

                <!-- Bouton Appel -->
                <a
                  v-if="product.client?.telephone"
                  :href="`tel:${product.client.telephone}`"
                  class="flex items-center justify-center gap-3 w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                >
                  <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                  Appeler maintenant
                </a>

                <!-- Message si pas de téléphone -->
                <p v-else class="text-center text-sm text-gray-500 mt-2">
                  Numéro de téléphone non disponible
                </p>
              </div>
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import axios from '@/api/axios'
import AppLayout from '@/components/layout/AppLayout.vue'
import { useCartStore } from '@/stores/cart'

const route = useRoute()
const cartStore = useCartStore()

const product = ref(null)
const loading = ref(true)
const error = ref(null)
const currentImage = ref('')
const showCommanderModal = ref(false)
const vuesCount = ref(0)
const viewRecorded = ref(false)
let viewTimer = null

// Images du produit
const productImages = computed(() => {
  if (!product.value) return []
  const images = [product.value.image_produits]
  if (product.value.image_produits1) images.push(product.value.image_produits1)
  if (product.value.image_produits2) images.push(product.value.image_produits2)
  return images.filter(Boolean)
})

// Calculer le ranking basé sur les vues (algorithme dynamique)
const productRanking = computed(() => {
  const vues = vuesCount.value || 0
  
  // Système de ranking dynamique basé sur les vues
  // 0-50 vues = 1 étoile
  // 51-100 vues = 2 étoiles
  // 101-200 vues = 3 étoiles
  // 201-400 vues = 4 étoiles
  // 401+ vues = 5 étoiles
  
  if (vues <= 50) {
    return 1
  } else if (vues <= 100) {
    return 2
  } else if (vues <= 200) {
    return 3
  } else if (vues <= 400) {
    return 4
  } else {
    return 5
  }
})

// URL WhatsApp avec message pré-rempli
const whatsappUrl = computed(() => {
  if (!product.value || !product.value.client?.telephone) return '#'
  
  const phone = product.value.client.telephone.replace(/\D/g, '')
  const message = `Bonjour, je voudrais avoir plus d'informations sur votre produit "${product.value.nom_produits}" dont le prix de vente est ${formatPrice(product.value.prix_produits)} qui a été posté sur le site ollyhansdistribution.com`
  
  return `https://wa.me/${phone}?text=${encodeURIComponent(message)}`
})

// Charger les détails du produit
const loadProduct = async () => {
  try {
    loading.value = true
    error.value = null
    
    const response = await axios.get(`/products/${route.params.id}`)
    
    if (response.success) {
      product.value = response.data
      vuesCount.value = response.data.vues_count || 0
      currentImage.value = product.value.image_produits
      
      // Démarrer le timer de 3 secondes pour enregistrer la vue
      startViewTimer()
    }
  } catch (err) {
    console.error('Erreur chargement produit:', err)
    if (err.response?.status === 404) {
      error.value = 'Ce produit n\'existe pas ou n\'est plus disponible.'
    } else {
      error.value = 'Une erreur est survenue lors du chargement du produit.'
    }
  } finally {
    loading.value = false
  }
}

// Démarrer le timer pour enregistrer la vue après 3 secondes
const startViewTimer = () => {
  if (viewRecorded.value) return
  
  viewTimer = setTimeout(() => {
    recordView()
  }, 3000)
}

// Enregistrer la vue
const recordView = async () => {
  if (viewRecorded.value || !product.value) return
  
  // Vérifier localStorage pour éviter les doublons (24h)
  const viewKey = `viewed_product_${product.value.id_produits}`
  const lastView = localStorage.getItem(viewKey)
  
  if (lastView) {
    const dayInMs = 24 * 60 * 60 * 1000
    if (Date.now() - parseInt(lastView) < dayInMs) {
      console.log('Vue déjà enregistrée aujourd\'hui')
      return
    }
  }
  
  try {
    const response = await axios.post(`/products/${product.value.id_produits}/view`)
    
    if (response.success) {
      viewRecorded.value = true
      vuesCount.value = response.vues_count
      localStorage.setItem(viewKey, Date.now().toString())
      console.log('✅ Vue enregistrée avec succès')
    }
  } catch (err) {
    // Ignorer silencieusement les erreurs 429 (déjà enregistré)
    if (err.response?.status !== 429) {
      console.error('Erreur enregistrement vue:', err)
    }
  }
}

// Ajouter au panier
const addToCart = () => {
  if (product.value) {
    cartStore.addItem(product.value)
  }
}

// Formater le prix
const formatPrice = (price) => {
  if (!price) return '0 FCFA'
  return `${parseInt(price).toLocaleString('fr-FR')} FCFA`
}

// Formater la date
const formatDate = (date) => {
  if (!date) return ''
  const options = { year: 'numeric', month: 'long', day: 'numeric' }
  return new Date(date).toLocaleDateString('fr-FR', options)
}

// Obtenir les initiales
const getInitials = (nom, prenom) => {
  const n = nom?.charAt(0) || ''
  const p = prenom?.charAt(0) || ''
  return `${p}${n}`.toUpperCase() || '?'
}

// Gérer les erreurs d'images
const handleImageError = (e) => {
  e.target.src = 'https://via.placeholder.com/400x400?text=Image+non+disponible'
}

// Lifecycle
onMounted(() => {
  loadProduct()
})

onUnmounted(() => {
  if (viewTimer) {
    clearTimeout(viewTimer)
  }
})

// Annuler le timer si l'utilisateur quitte avant 3s
watch(() => route.path, () => {
  if (viewTimer) {
    clearTimeout(viewTimer)
  }
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

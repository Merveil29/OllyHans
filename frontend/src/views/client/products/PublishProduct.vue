<template>
  <div class="min-h-screen bg-gray-50">
    <DashboardHeader />
    <div class="max-w-4xl mx-auto py-8 px-4 space-y-6">
      <!-- Info jetons -->
      <div class="bg-gradient-to-br from-warning-50 to-warning-100 border border-warning-200 rounded-lg p-4">
        <div class="flex items-start gap-3">
          <svg class="w-6 h-6 text-warning-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div>
            <p class="font-semibold text-warning-900 mb-1">
              Coût : 1 jeton client
            </p>
            <p class="text-sm text-warning-800">
              Vous avez actuellement <strong>{{ jetonsRestants }} jeton{{ jetonsRestants > 1 ? 's' : '' }}</strong> disponible{{ jetonsRestants > 1 ? 's' : '' }}.
              Après publication, votre produit sera soumis à validation par un administrateur.
            </p>
          </div>
        </div>
      </div>

      <!-- Formulaire -->
      <form @submit.prevent="handleSubmit" class="bg-white rounded-lg shadow-sm p-6 space-y-6">
        <!-- Messages -->
        <div v-if="errorMessage" class="bg-error-50 border-l-4 border-error-500 p-4 rounded">
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-error-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm text-error-700">{{ errorMessage }}</p>
          </div>
        </div>

        <div v-if="successMessage" class="bg-success-50 border-l-4 border-success-500 p-4 rounded">
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-success-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm text-success-700">{{ successMessage }}</p>
          </div>
        </div>

        <!-- Nom du produit -->
        <div>
          <label for="nom_produits" class="block text-sm font-medium text-gray-700 mb-2">
            Nom du produit *
          </label>
          <input
            id="nom_produits"
            v-model="formData.nom_produits"
            type="text"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
            placeholder="Ex: iPhone 14 Pro Max"
          />
        </div>

        <!-- Prix -->
        <div>
          <label for="prix_produits" class="block text-sm font-medium text-gray-700 mb-2">
            Prix (FCFA) *
          </label>
          <input
            id="prix_produits"
            v-model.number="formData.prix_produits"
            type="number"
            min="0"
            step="1"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
            placeholder="Ex: 850000"
          />
        </div>

        <!-- Catégorie et Sous-catégorie -->
        <div>
          <label for="id_sous_categorie" class="block text-sm font-medium text-gray-700 mb-2">
            Catégorie / Sous-catégorie *
          </label>
          <select
            id="id_sous_categorie"
            v-model="formData.id_sous_categorie"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
          >
            <option value="">Sélectionner une sous-catégorie</option>
            <optgroup v-for="category in categoriesWithSubs" :key="category.id_categorie" :label="category.nom_categorie">
              <option v-for="sub in category.sous_categories" :key="sub.id_sous_categorie" :value="sub.id_sous_categorie">
                {{ sub.libelle_sous_categorie }}
              </option>
            </optgroup>
          </select>
        </div>

        <!-- Description -->
        <div>
          <label for="description_produits" class="block text-sm font-medium text-gray-700 mb-2">
            Description *
          </label>
          <textarea
            id="description_produits"
            v-model="formData.description_produits"
            rows="5"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors resize-none"
            placeholder="Décrivez votre produit en détail..."
          />
        </div>

        <!-- Images (3 images) -->
        <div class="space-y-4">
          <h3 class="text-lg font-semibold text-gray-900">Images du produit</h3>
          
          <!-- Image principale -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Image principale * (800x800px recommandé)
            </label>
            
            <div v-if="imagePreviews.main" class="mb-4 relative">
              <img
                :src="imagePreviews.main"
                alt="Image principale"
                class="w-full h-64 object-contain bg-gray-50 rounded-lg border-2 border-dashed border-gray-300"
              />
              <button
                type="button"
                @click="removeImage('main')"
                class="absolute top-2 right-2 bg-error-600 text-white p-2 rounded-full hover:bg-error-700 transition-colors shadow-lg"
              >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <div
              v-else
              @click="$refs.fileInputMain.click()"
              class="border-2 border-dashed rounded-lg p-8 text-center transition-colors cursor-pointer border-gray-300 hover:border-gray-400"
            >
              <input
                ref="fileInputMain"
                type="file"
                accept="image/*"
                class="hidden"
                @change="(e) => handleFileSelect(e, 'main')"
              />
              <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
              </svg>
              <p class="text-gray-700 font-medium mb-1">
                Cliquez pour choisir l'image principale
              </p>
              <p class="text-sm text-gray-500">PNG, JPG jusqu'à 5MB</p>
            </div>
          </div>

          <!-- Images secondaires -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Image 1 -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Image 2 (optionnel)
              </label>
              
              <div v-if="imagePreviews.image1" class="relative">
                <img
                  :src="imagePreviews.image1"
                  alt="Image 2"
                  class="w-full h-48 object-contain bg-gray-50 rounded-lg border-2 border-dashed border-gray-300"
                />
                <button
                  type="button"
                  @click="removeImage('image1')"
                  class="absolute top-2 right-2 bg-error-600 text-white p-2 rounded-full hover:bg-error-700 transition-colors shadow-lg"
                >
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <div
                v-else
                @click="$refs.fileInputImage1.click()"
                class="border-2 border-dashed rounded-lg p-6 text-center transition-colors cursor-pointer border-gray-300 hover:border-gray-400"
              >
                <input
                  ref="fileInputImage1"
                  type="file"
                  accept="image/*"
                  class="hidden"
                  @change="(e) => handleFileSelect(e, 'image1')"
                />
                <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <p class="text-sm text-gray-600">Ajouter une image</p>
              </div>
            </div>

            <!-- Image 2 -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Image 3 (optionnel)
              </label>
              
              <div v-if="imagePreviews.image2" class="relative">
                <img
                  :src="imagePreviews.image2"
                  alt="Image 3"
                  class="w-full h-48 object-contain bg-gray-50 rounded-lg border-2 border-dashed border-gray-300"
                />
                <button
                  type="button"
                  @click="removeImage('image2')"
                  class="absolute top-2 right-2 bg-error-600 text-white p-2 rounded-full hover:bg-error-700 transition-colors shadow-lg"
                >
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <div
                v-else
                @click="$refs.fileInputImage2.click()"
                class="border-2 border-dashed rounded-lg p-6 text-center transition-colors cursor-pointer border-gray-300 hover:border-gray-400"
              >
                <input
                  ref="fileInputImage2"
                  type="file"
                  accept="image/*"
                  class="hidden"
                  @change="(e) => handleFileSelect(e, 'image2')"
                />
                <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <p class="text-sm text-gray-600">Ajouter une image</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Boutons -->
        <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t">
          <button
            type="button"
            @click="$router.push('/suivi/produits')"
            class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 font-medium"
          >
            Annuler
          </button>
          <button
            type="submit"
            :disabled="loading || jetonsRestants < 1 || !formData.image_produits || !formData.id_sous_categorie"
            class="flex-1 px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors duration-200 font-semibold disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
          >
            <svg v-if="loading" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span v-if="loading">Publication...</span>
            <span v-else>Publier mon produit</span>
          </button>
        </div>

        <p class="text-xs text-gray-500 text-center pt-2">
          * Champs obligatoires
        </p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { productsAPI } from '@/api/modules/products'
import { getAllSubCategories } from '@/api/modules/admin/subCategories'
import DashboardHeader from '@/components/layout/DashboardHeader.vue'

const router = useRouter()
const authStore = useAuthStore()

const formData = ref({
  nom_produits: '',
  prix_produits: '',
  description_produits: '',
  id_sous_categorie: '',
  image_produits: null,
  image_produits1: null,
  image_produits2: null
})

const imagePreviews = ref({
  main: null,
  image1: null,
  image2: null
})

const categoriesWithSubs = ref([])
const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const fileInputMain = ref(null)
const fileInputImage1 = ref(null)
const fileInputImage2 = ref(null)

const jetonsRestants = computed(() => authStore.user?.jettons || 0)

const loadSubCategories = async () => {
  try {
    const response = await getAllSubCategories()
    if (response.success && response.data) {
      categoriesWithSubs.value = response.data
    }
  } catch (error) {
    console.error('Erreur chargement sous-catégories:', error)
    errorMessage.value = 'Erreur lors du chargement des catégories'
  }
}

const handleFileSelect = (event, type) => {
  const file = event.target.files[0]
  if (file) {
    processFile(file, type)
  }
}

const processFile = (file, type) => {
  if (!file.type.startsWith('image/')) {
    errorMessage.value = 'Veuillez sélectionner une image valide'
    return
  }

  if (file.size > 5 * 1024 * 1024) {
    errorMessage.value = 'L\'image ne doit pas dépasser 5MB'
    return
  }

  // Assigner le fichier aux bonnes propriétés
  if (type === 'main') {
    formData.value.image_produits = file
  } else if (type === 'image1') {
    formData.value.image_produits1 = file
  } else if (type === 'image2') {
    formData.value.image_produits2 = file
  }
  
  // Créer la prévisualisation
  const reader = new FileReader()
  reader.onload = (e) => {
    imagePreviews.value[type] = e.target.result
  }
  reader.readAsDataURL(file)
  
  errorMessage.value = ''
}

const removeImage = (type) => {
  if (type === 'main') {
    formData.value.image_produits = null
    if (fileInputMain.value) fileInputMain.value.value = ''
  } else if (type === 'image1') {
    formData.value.image_produits1 = null
    if (fileInputImage1.value) fileInputImage1.value.value = ''
  } else if (type === 'image2') {
    formData.value.image_produits2 = null
    if (fileInputImage2.value) fileInputImage2.value.value = ''
  }
  
  imagePreviews.value[type] = null
}

const handleSubmit = async () => {
  errorMessage.value = ''
  successMessage.value = ''

  // Validations
  if (!formData.value.nom_produits.trim()) {
    errorMessage.value = 'Le nom du produit est obligatoire'
    return
  }

  if (!formData.value.prix_produits || formData.value.prix_produits < 0) {
    errorMessage.value = 'Le prix doit être supérieur ou égal à 0'
    return
  }

  if (!formData.value.description_produits.trim()) {
    errorMessage.value = 'La description est obligatoire'
    return
  }

  if (!formData.value.id_sous_categorie) {
    errorMessage.value = 'La sous-catégorie est obligatoire'
    return
  }

  if (!formData.value.image_produits) {
    errorMessage.value = 'L\'image principale est obligatoire'
    return
  }

  if (jetonsRestants.value < 1) {
    errorMessage.value = 'Vous n\'avez pas assez de jetons client'
    return
  }

  loading.value = true

  try {
    const data = new FormData()
    data.append('nom_produits', formData.value.nom_produits.trim())
    data.append('prix_produits', formData.value.prix_produits)
    data.append('description_produits', formData.value.description_produits.trim())
    data.append('id_sous_categorie', formData.value.id_sous_categorie)
    data.append('image_produits', formData.value.image_produits)
    
    if (formData.value.image_produits1) {
      data.append('image_produits1', formData.value.image_produits1)
    }
    
    if (formData.value.image_produits2) {
      data.append('image_produits2', formData.value.image_produits2)
    }

    const response = await productsAPI.createProduct(data)

    if (response.success) {
      successMessage.value = 'Produit créé avec succès ! En attente de validation...'
      
      // Mettre à jour les jetons dans le store
      await authStore.fetchProfile()
      
      // Rediriger après 2 secondes
      setTimeout(() => {
        router.push('/suivi/produits')
      }, 2000)
    }
  } catch (error) {
    console.error('Erreur lors de la création:', error)
    errorMessage.value = error.response?.data?.message || 'Une erreur est survenue lors de la publication'
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await authStore.fetchUser()
  loadSubCategories()
})
</script>

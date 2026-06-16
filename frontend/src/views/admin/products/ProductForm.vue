<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
          {{ isEdit ? 'Modifier le produit' : 'Nouveau produit' }}
        </h1>
        <p class="text-sm text-gray-600 mt-1">
          {{ isEdit ? 'Modifier les informations du produit' : 'Créer un nouveau produit' }}
        </p>
      </div>
    </div>

    <div v-if="errorMessage" class="bg-error-50 border-l-4 border-error-500 p-4 rounded">
      <p class="text-sm text-error-700">{{ errorMessage }}</p>
    </div>

    <form @submit.prevent="handleSubmit" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label for="nom_produits" class="block text-sm font-medium text-gray-700 mb-2">Nom du produit *</label>
          <input id="nom_produits" v-model="form.nom_produits" type="text" required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            placeholder="Ex: iPhone 14 Pro Max" />
        </div>
        <div>
          <label for="prix_produits" class="block text-sm font-medium text-gray-700 mb-2">Prix (FCFA) *</label>
          <input id="prix_produits" v-model.number="form.prix_produits" type="number" min="0" required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            placeholder="Ex: 850000" />
        </div>
      </div>

      <div>
        <label for="id_categorie" class="block text-sm font-medium text-gray-700 mb-2">Catégorie *</label>
        <select id="id_categorie" v-model="form.id_categorie" required
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
          <option value="">Sélectionner une catégorie</option>
          <option v-for="cat in categories" :key="cat.id_categorie" :value="cat.id_categorie">
            {{ cat.nom_categorie }}
          </option>
        </select>
      </div>

      <div>
        <label for="description_produits" class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
        <textarea id="description_produits" v-model="form.description_produits" rows="5" required
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 resize-none"
          placeholder="Décrivez le produit..."></textarea>
      </div>

      <div>
        <label for="id_state" class="block text-sm font-medium text-gray-700 mb-2">État</label>
        <select id="id_state" v-model="form.id_state"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
          <option :value="1">En attente</option>
          <option :value="2">Publié</option>
          <option :value="3">Rejeté</option>
          <option :value="4">Premium</option>
          <option :value="5">Désactivé</option>
        </select>
      </div>

      <div class="space-y-4">
        <h3 class="text-lg font-semibold text-gray-900">Images</h3>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Image principale</label>
          <div v-if="previews.main" class="mb-4 relative inline-block">
            <img :src="previews.main" class="h-48 object-contain bg-gray-50 rounded-lg border-2 border-dashed border-gray-300" />
            <button type="button" @click="removeImage('main')"
              class="absolute top-2 right-2 bg-error-600 text-white p-1.5 rounded-full hover:bg-error-700">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <div v-else @click="$refs.fileMain.click()"
            class="border-2 border-dashed rounded-lg p-8 text-center cursor-pointer border-gray-300 hover:border-gray-400">
            <input ref="fileMain" type="file" accept="image/*" class="hidden" @change="(e) => handleFile(e, 'main')" />
            <p class="text-gray-600">Cliquez pour choisir une image</p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Image 2 (optionnelle)</label>
            <div v-if="previews.image1" class="mb-4 relative inline-block">
              <img :src="previews.image1" class="h-32 object-contain bg-gray-50 rounded-lg border-2 border-dashed border-gray-300" />
              <button type="button" @click="removeImage('image1')"
                class="absolute top-2 right-2 bg-error-600 text-white p-1.5 rounded-full hover:bg-error-700">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <div v-else @click="$refs.fileImage1.click()"
              class="border-2 border-dashed rounded-lg p-6 text-center cursor-pointer border-gray-300 hover:border-gray-400">
              <input ref="fileImage1" type="file" accept="image/*" class="hidden" @change="(e) => handleFile(e, 'image1')" />
              <p class="text-sm text-gray-600">Ajouter une image</p>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Image 3 (optionnelle)</label>
            <div v-if="previews.image2" class="mb-4 relative inline-block">
              <img :src="previews.image2" class="h-32 object-contain bg-gray-50 rounded-lg border-2 border-dashed border-gray-300" />
              <button type="button" @click="removeImage('image2')"
                class="absolute top-2 right-2 bg-error-600 text-white p-1.5 rounded-full hover:bg-error-700">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <div v-else @click="$refs.fileImage2.click()"
              class="border-2 border-dashed rounded-lg p-6 text-center cursor-pointer border-gray-300 hover:border-gray-400">
              <input ref="fileImage2" type="file" accept="image/*" class="hidden" @change="(e) => handleFile(e, 'image2')" />
              <p class="text-sm text-gray-600">Ajouter une image</p>
            </div>
          </div>
        </div>
      </div>

      <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t">
        <button type="button" @click="$router.push('/admin/products')"
          class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
          Annuler
        </button>
        <button type="submit" :disabled="loading"
          class="flex-1 px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-semibold disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
          <svg v-if="loading" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
          </svg>
          <span>{{ loading ? 'Enregistrement...' : isEdit ? 'Enregistrer les modifications' : 'Créer le produit' }}</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { createProduct, updateProduct, getProduct } from '@/api/modules/admin/products'
import { getCategories } from '@/api/modules/admin/categories'

const route = useRoute()
const router = useRouter()

const isEdit = computed(() => !!route.params.id)
const loading = ref(false)
const errorMessage = ref('')

const categories = ref([])

const form = ref({
  nom_produits: '',
  prix_produits: '',
  description_produits: '',
  id_categorie: '',
  id_state: 1
})

const files = ref({
  main: null,
  image1: null,
  image2: null
})

const previews = ref({
  main: null,
  image1: null,
  image2: null
})

const existingImages = ref({
  main: null,
  image1: null,
  image2: null
})

const handleFile = (event, type) => {
  const file = event.target.files[0]
  if (!file) return
  if (!file.type.startsWith('image/')) { errorMessage.value = 'Format d\'image invalide'; return }
  if (file.size > 5 * 1024 * 1024) { errorMessage.value = 'L\'image ne doit pas dépasser 5MB'; return }

  const key = type === 'main' ? 'main' : type
  files.value[key] = file

  const reader = new FileReader()
  reader.onload = (e) => { previews.value[key] = e.target.result }
  reader.readAsDataURL(file)
  errorMessage.value = ''
}

const removeImage = (type) => {
  const key = type === 'main' ? 'main' : type
  files.value[key] = null
  previews.value[key] = null
  if (key === 'main' && existingImages.value.main) existingImages.value.main = null
  if (key === 'image1' && existingImages.value.image1) existingImages.value.image1 = null
  if (key === 'image2' && existingImages.value.image2) existingImages.value.image2 = null
}

const loadCategories = async () => {
  try {
    const response = await getCategories()
    if (response.success && response.data) {
      categories.value = response.data
    }
  } catch (error) {
    console.error('Erreur chargement catégories:', error)
  }
}

const loadProduct = async () => {
  if (!isEdit.value) return
  try {
    const response = await getProduct(route.params.id)
    if (response.success && response.data) {
      form.value.nom_produits = response.data.nom_produits || ''
      form.value.prix_produits = response.data.prix_produits || ''
      form.value.description_produits = response.data.description_produits || ''
      form.value.id_categorie = response.data.id_categorie || ''
      form.value.id_state = response.data.state?.id_state || 1

      if (response.data.image_produits) {
        previews.value.main = response.data.image_produits
        existingImages.value.main = response.data.image_produits
      }
      if (response.data.image_produits1) {
        previews.value.image1 = response.data.image_produits1
        existingImages.value.image1 = response.data.image_produits1
      }
      if (response.data.image_produits2) {
        previews.value.image2 = response.data.image_produits2
        existingImages.value.image2 = response.data.image_produits2
      }
    }
  } catch (error) {
    errorMessage.value = 'Erreur lors du chargement du produit'
  }
}

const handleSubmit = async () => {
  errorMessage.value = ''

  if (!form.value.nom_produits.trim()) { errorMessage.value = 'Le nom du produit est obligatoire'; return }
  if (!form.value.prix_produits || form.value.prix_produits < 0) { errorMessage.value = 'Prix invalide'; return }
  if (!form.value.description_produits.trim()) { errorMessage.value = 'La description est obligatoire'; return }
  if (!form.value.id_categorie) { errorMessage.value = 'La catégorie est obligatoire'; return }

  loading.value = true
  try {
    const fd = new FormData()
    fd.append('nom_produits', form.value.nom_produits.trim())
    fd.append('prix_produits', String(form.value.prix_produits))
    fd.append('description_produits', form.value.description_produits.trim())
    fd.append('id_categorie', String(form.value.id_categorie))
    fd.append('id_state', String(form.value.id_state))

    if (files.value.main) fd.append('image_produits', files.value.main)
    if (files.value.image1) fd.append('image_produits1', files.value.image1)
    if (files.value.image2) fd.append('image_produits2', files.value.image2)

    if (isEdit.value) {
      await updateProduct(route.params.id, fd)
    } else {
      await createProduct(fd)
    }
    router.push('/admin/products')
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Une erreur est survenue'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadCategories()
  if (isEdit.value) loadProduct()
})
</script>

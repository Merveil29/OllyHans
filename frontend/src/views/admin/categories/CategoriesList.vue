<template>
  <div class="min-h-screen bg-gray-50 p-3 sm:p-4 md:p-6">
    <!-- Header Principal - Uniquement sur la page principale -->
    <div v-if="currentView === 'categories'" class="mb-6 md:mb-8">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Gestion des Catégories</h1>
          <p class="mt-1 text-xs sm:text-sm text-gray-500">
            Gérez les catégories et sous-catégories de produits
          </p>
        </div>
        <button
          @click="openCreateCategoryModal"
          class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 font-medium transition-colors text-sm sm:text-base whitespace-nowrap"
        >
          <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          <span class="hidden sm:inline">Nouvelle Catégorie</span>
          <span class="sm:hidden">Nouveau</span>
        </button>
      </div>
    </div>

    <!-- Breadcrumb -->
    <div v-if="breadcrumbs.length > 1" class="mb-4 md:mb-6">
      <nav class="flex overflow-x-auto" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 sm:space-x-2">
          <li v-for="(crumb, index) in breadcrumbs" :key="index" class="inline-flex items-center">
            <button
              v-if="index < breadcrumbs.length - 1"
              @click="navigateToBreadcrumb(index)"
              class="inline-flex items-center text-xs sm:text-sm font-medium text-primary-600 hover:text-primary-800"
            >
              {{ crumb.name }}
            </button>
            <span v-else class="text-xs sm:text-sm font-medium text-gray-500">{{ crumb.name }}</span>
            <svg v-if="index < breadcrumbs.length - 1" class="w-3 h-3 sm:w-4 sm:h-4 mx-1 sm:mx-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
          </li>
        </ol>
      </nav>
    </div>

    <!-- Alert Messages -->
    <div v-if="successMessage" class="mb-4 sm:mb-6 bg-green-50 border-l-4 border-green-500 p-3 sm:p-4 rounded">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-4 w-4 sm:h-5 sm:w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-xs sm:text-sm text-green-700">{{ successMessage }}</p>
        </div>
      </div>
    </div>

    <div v-if="errorMessage" class="mb-4 sm:mb-6 bg-red-50 border-l-4 border-red-500 p-3 sm:p-4 rounded">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-4 w-4 sm:h-5 sm:w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-xs sm:text-sm text-red-700">{{ errorMessage }}</p>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center py-12">
      <svg class="animate-spin h-12 w-12 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </div>

    <!-- Categories Grid -->
    <div v-else-if="currentView === 'categories'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div
        v-for="categorie in categories"
        :key="categorie.id_categorie"
        class="bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-300 cursor-pointer border border-gray-200 transform hover:-translate-y-1"
        @click="viewSubCategories(categorie)"
      >
        <div class="relative h-48 bg-gray-100 rounded-t-lg overflow-hidden">
          <img
            :src="getImageUrl(categorie.image_categorie)"
            :alt="categorie.nom_categorie"
            class="w-full h-full object-cover"
            @error="handleImageError"
          />
        </div>
          <div class="p-3 sm:p-4">
          <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2 truncate">{{ categorie.nom_categorie }}</h3>
          <div class="flex items-center justify-between text-xs sm:text-sm text-gray-500 mb-3 sm:mb-4">
            <span class="truncate">{{ categorie.sous_categories_count || 0 }} sous-cat.</span>
            <span class="truncate">{{ categorie.produits_count || 0 }} prod.</span>
          </div>
          <div class="flex items-center justify-end space-x-1 sm:space-x-2">
            <button
              v-if="categorie.can_modify"
              @click.stop="openEditCategoryModal(categorie)"
              class="p-1.5 sm:p-2 text-blue-600 hover:bg-blue-50 rounded-md transition-colors"
              title="Modifier"
            >
              <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
            </button>
            <button
              v-if="categorie.can_delete"
              @click.stop="confirmDeleteCategory(categorie)"
              class="p-1.5 sm:p-2 text-red-600 hover:bg-red-50 rounded-md transition-colors"
              title="Supprimer"
            >
              <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
            <span v-else class="text-[10px] sm:text-xs text-gray-400 italic whitespace-nowrap">A des produits</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Sub-Categories List -->
    <div v-else-if="currentView === 'subCategories'" class="bg-white rounded-lg shadow-sm">
      <div class="p-4 sm:p-6 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
          <h2 class="text-lg sm:text-xl font-semibold text-gray-900">
            Sous-catégories de {{ selectedCategory?.nom_categorie }}
          </h2>
          <button
            @click="openCreateSubCategoryModal"
            class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 font-medium transition-colors text-sm sm:text-base whitespace-nowrap"
          >
            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span class="hidden sm:inline">Nouvelle Sous-Catégorie</span>
            <span class="sm:hidden">Nouveau</span>
          </button>
        </div>
      </div>
      <div class="p-4 sm:p-6">
        <div v-if="subCategories.length === 0" class="text-center py-8 sm:py-12 text-gray-500">
          <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
          </svg>
          <p class="text-sm sm:text-base">Aucune sous-catégorie</p>
        </div>
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
          <div
            v-for="subCat in subCategories"
            :key="subCat.id_sous_categorie"
            @click="viewProducts(subCat)"
            class="p-3 sm:p-4 border border-gray-200 rounded-lg hover:border-primary-500 hover:shadow-md transition-all cursor-pointer"
          >
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 sm:gap-2 mb-2">
              <h3 class="text-base sm:text-lg font-medium text-gray-900 truncate">{{ subCat.libelle_sous_categorie }}</h3>
              <span class="text-xs sm:text-sm text-gray-500 whitespace-nowrap">{{ subCat.produits_count || 0 }} produits</span>
            </div>
            <div class="flex items-center justify-end space-x-1 sm:space-x-2 mt-3 sm:mt-4">
              <button
                v-if="subCat.can_modify"
                @click.stop="openEditSubCategoryModal(subCat)"
                class="p-1.5 sm:p-2 text-blue-600 hover:bg-blue-50 rounded-md transition-colors"
                title="Modifier"
              >
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              </button>
              <button
                v-if="subCat.can_delete"
                @click.stop="confirmDeleteSubCategory(subCat)"
                class="p-1.5 sm:p-2 text-red-600 hover:bg-red-50 rounded-md transition-colors"
                title="Supprimer"
              >
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
              <span v-else class="text-[10px] sm:text-xs text-gray-400 italic whitespace-nowrap">A des produits</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Products List -->
    <div v-else-if="currentView === 'products'" class="bg-white rounded-lg shadow-sm">
      <div class="p-4 sm:p-6 border-b border-gray-200">
        <h2 class="text-lg sm:text-xl font-semibold text-gray-900">
          Produits de {{ selectedSubCategory?.libelle_sous_categorie }}
        </h2>
      </div>
      <div class="p-4 sm:p-6">
        <div v-if="products.length === 0" class="text-center py-8 sm:py-12 text-gray-500">
          <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
          </svg>
          <p class="text-sm sm:text-base">Aucun produit</p>
        </div>
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <div
            v-for="produit in products"
            :key="produit.id_produits"
            class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden"
          >
            <div class="relative h-48 bg-gray-100">
              <img
                :src="getImageUrl(produit.image_produits)"
                :alt="produit.nom_produits"
                class="w-full h-full object-cover"
                @error="handleImageError"
              />
            </div>
            <div class="p-3 sm:p-4">
              <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2 truncate">{{ produit.nom_produits }}</h3>
              <p class="text-sm sm:text-base text-primary-600 font-bold">{{ produit.prix_produits }} FCFA</p>
              <span
                :class="[
                  'inline-block mt-2 px-2 py-1 text-xs rounded-full',
                  produit.id_state === 2 ? 'bg-green-100 text-green-800' :
                  produit.id_state === 1 ? 'bg-yellow-100 text-yellow-800' :
                  'bg-red-100 text-red-800'
                ]"
              >
                {{ produit.id_state === 2 ? 'Validé' : produit.id_state === 1 ? 'En attente' : 'Rejeté' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Category Modal -->
    <div v-if="showCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-3 sm:p-4">
      <div class="bg-white rounded-lg max-w-md w-full p-4 sm:p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg sm:text-xl font-semibold mb-3 sm:mb-4">
          {{ categoryModalMode === 'create' ? 'Nouvelle Catégorie' : 'Modifier la Catégorie' }}
        </h3>
        <form @submit.prevent="saveCategory">
          <div class="mb-3 sm:mb-4">
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Nom de la catégorie</label>
            <input
              v-model="categoryForm.nom_categorie"
              type="text"
              required
              class="w-full px-3 py-2 text-sm sm:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
              placeholder="Ex: Électronique"
            />
          </div>
          <div class="mb-3 sm:mb-4">
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Image</label>
            <input
              ref="categoryImageInput"
              type="file"
              accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
              @change="handleCategoryImageSelect"
              class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
              :required="categoryModalMode === 'create'"
            />
            <p class="text-[10px] sm:text-xs text-gray-500 mt-1">JPG, PNG, GIF ou WEBP (Max. 5MB)</p>
          </div>
          <div v-if="categoryForm.previewImage" class="mb-3 sm:mb-4">
            <img :src="categoryForm.previewImage" alt="Preview" class="w-full h-32 sm:h-48 object-cover rounded-md" />
          </div>
          <div class="flex flex-col sm:flex-row justify-end gap-2 sm:gap-3">
            <button
              type="button"
              @click="closeCategoryModal"
              class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 text-sm sm:text-base"
            >
              Annuler
            </button>
            <button
              type="submit"
              :disabled="savingCategory"
              class="w-full sm:w-auto px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 disabled:opacity-50 text-sm sm:text-base"
            >
              {{ savingCategory ? 'Enregistrement...' : 'Enregistrer' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Create/Edit Sub-Category Modal -->
    <div v-if="showSubCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-3 sm:p-4">
      <div class="bg-white rounded-lg max-w-md w-full p-4 sm:p-6">
        <h3 class="text-lg sm:text-xl font-semibold mb-3 sm:mb-4">
          {{ subCategoryModalMode === 'create' ? 'Nouvelle Sous-Catégorie' : 'Modifier la Sous-Catégorie' }}
        </h3>
        <form @submit.prevent="saveSubCategory">
          <div class="mb-3 sm:mb-4">
            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Nom de la sous-catégorie</label>
            <input
              v-model="subCategoryForm.libelle_sous_categorie"
              type="text"
              required
              class="w-full px-3 py-2 text-sm sm:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
              placeholder="Ex: Smartphones"
            />
          </div>
          <div class="flex flex-col sm:flex-row justify-end gap-2 sm:gap-3">
            <button
              type="button"
              @click="closeSubCategoryModal"
              class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 text-sm sm:text-base"
            >
              Annuler
            </button>
            <button
              type="submit"
              :disabled="savingSubCategory"
              class="w-full sm:w-auto px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 disabled:opacity-50 text-sm sm:text-base"
            >
              {{ savingSubCategory ? 'Enregistrement...' : 'Enregistrer' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-3 sm:p-4">
      <div class="bg-white rounded-lg max-w-md w-full p-4 sm:p-6">
        <h3 class="text-lg sm:text-xl font-semibold mb-3 sm:mb-4 text-red-600">Confirmer la suppression</h3>
        <p class="text-sm sm:text-base text-gray-700 mb-4 sm:mb-6">
          Êtes-vous sûr de vouloir supprimer {{ deleteTarget?.type === 'category' ? 'cette catégorie' : 'cette sous-catégorie' }} ?
          Cette action est irréversible.
        </p>
        <div class="flex flex-col sm:flex-row justify-end gap-2 sm:gap-3">
          <button
            type="button"
            @click="closeDeleteModal"
            class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 text-sm sm:text-base"
          >
            Annuler
          </button>
          <button
            @click="executeDelete"
            :disabled="deleting"
            class="w-full sm:w-auto px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 disabled:opacity-50 text-sm sm:text-base"
          >
            {{ deleting ? 'Suppression...' : 'Supprimer' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import * as categoriesAPI from '@/api/modules/admin/categories'
import * as subCategoriesAPI from '@/api/modules/admin/subCategories'

// State
const loading = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
const currentView = ref('categories') // 'categories', 'subCategories', 'products'
const categories = ref([])
const subCategories = ref([])
const products = ref([])

const selectedCategory = ref(null)
const selectedSubCategory = ref(null)

// Breadcrumbs
const breadcrumbs = computed(() => {
  const crumbs = [{ name: 'Catégories', view: 'categories' }]
  if (selectedCategory.value) {
    crumbs.push({ name: selectedCategory.value.nom_categorie, view: 'subCategories' })
  }
  if (selectedSubCategory.value) {
    crumbs.push({ name: selectedSubCategory.value.libelle_sous_categorie, view: 'products' })
  }
  return crumbs
})

// Category Modal
const showCategoryModal = ref(false)
const categoryModalMode = ref('create') // 'create' or 'edit'
const savingCategory = ref(false)
const categoryForm = ref({
  id_categorie: null,
  nom_categorie: '',
  imageFile: null,
  previewImage: null
})

// Sub-Category Modal
const showSubCategoryModal = ref(false)
const subCategoryModalMode = ref('create')
const savingSubCategory = ref(false)
const subCategoryForm = ref({
  id_sous_categorie: null,
  libelle_sous_categorie: '',
  id_categorie: null
})

// Delete Modal
const showDeleteModal = ref(false)
const deleting = ref(false)
const deleteTarget = ref(null)

// Methods
const fetchCategories = async () => {
  loading.value = true
  try {
    const response = await categoriesAPI.getCategories()
    categories.value = response.data || []
  } catch (error) {
    console.error('Erreur lors du chargement des catégories:', error)
    const message = error.response?.data?.message || error.message || 'Erreur lors du chargement des catégories'
    errorMessage.value = message
    setTimeout(() => errorMessage.value = '', 5000)
  } finally {
    loading.value = false
  }
}

const viewSubCategories = async (categorie) => {
  selectedCategory.value = categorie
  loading.value = true
  try {
    const response = await categoriesAPI.getCategory(categorie.id_categorie)
    subCategories.value = response.data?.sous_categories || []
    currentView.value = 'subCategories'
  } catch (error) {
    console.error('Erreur lors du chargement des sous-catégories:', error)
    const message = error.response?.data?.message || error.message || 'Erreur lors du chargement des sous-catégories'
    errorMessage.value = message
    setTimeout(() => errorMessage.value = '', 5000)
  } finally {
    loading.value = false
  }
}

const viewProducts = async (subCategory) => {
  selectedSubCategory.value = subCategory
  loading.value = true
  try {
    const response = await subCategoriesAPI.getSubCategory(subCategory.id_sous_categorie)
    products.value = response.data?.produits || []
    currentView.value = 'products'
  } catch (error) {
    console.error('Erreur lors du chargement des produits:', error)
    const message = error.response?.data?.message || error.message || 'Erreur lors du chargement des produits'
    errorMessage.value = message
    setTimeout(() => errorMessage.value = '', 5000)
  } finally {
    loading.value = false
  }
}

const navigateToBreadcrumb = (index) => {
  if (index === 0) {
    currentView.value = 'categories'
    selectedCategory.value = null
    selectedSubCategory.value = null
  } else if (index === 1) {
    currentView.value = 'subCategories'
    selectedSubCategory.value = null
  }
}

const openCreateCategoryModal = () => {
  categoryModalMode.value = 'create'
  categoryForm.value = {
    id_categorie: null,
    nom_categorie: '',
    imageFile: null,
    previewImage: null
  }
  showCategoryModal.value = true
}

const openEditCategoryModal = (categorie) => {
  categoryModalMode.value = 'edit'
  categoryForm.value = {
    id_categorie: categorie.id_categorie,
    nom_categorie: categorie.nom_categorie,
    imageFile: null,
    previewImage: getImageUrl(categorie.image_categorie)
  }
  showCategoryModal.value = true
}

const closeCategoryModal = () => {
  showCategoryModal.value = false
  categoryForm.value = {
    id_categorie: null,
    nom_categorie: '',
    imageFile: null,
    previewImage: null
  }
}

const handleCategoryImageSelect = (event) => {
  const file = event.target.files[0]
  if (!file) return

  // Validate file size (5MB max)
  if (file.size > 5 * 1024 * 1024) {
    errorMessage.value = 'L\'image ne doit pas dépasser 5MB'
    setTimeout(() => errorMessage.value = '', 3000)
    return
  }

  categoryForm.value.imageFile = file

  // Create preview
  const reader = new FileReader()
  reader.onload = (e) => {
    categoryForm.value.previewImage = e.target.result
  }
  reader.readAsDataURL(file)
}

const saveCategory = async () => {
  savingCategory.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    const formData = new FormData()
    formData.append('nom_categorie', categoryForm.value.nom_categorie)
    if (categoryForm.value.imageFile) {
      formData.append('image', categoryForm.value.imageFile)
    }

    if (categoryModalMode.value === 'create') {
      await categoriesAPI.createCategory(formData)
      successMessage.value = 'Catégorie créée avec succès'
    } else {
      await categoriesAPI.updateCategory(categoryForm.value.id_categorie, formData)
      successMessage.value = 'Catégorie mise à jour avec succès'
    }

    closeCategoryModal()
    await fetchCategories()
    setTimeout(() => successMessage.value = '', 3000)
  } catch (error) {
    console.error('Erreur lors de l\'enregistrement de la catégorie:', error)
    const message = error.response?.data?.message || error.message || 'Erreur lors de l\'enregistrement de la catégorie'
    errorMessage.value = message
    setTimeout(() => errorMessage.value = '', 5000)
  } finally {
    savingCategory.value = false
  }
}

const openCreateSubCategoryModal = () => {
  subCategoryModalMode.value = 'create'
  subCategoryForm.value = {
    id_sous_categorie: null,
    libelle_sous_categorie: '',
    id_categorie: selectedCategory.value.id_categorie
  }
  showSubCategoryModal.value = true
}

const openEditSubCategoryModal = (subCategory) => {
  subCategoryModalMode.value = 'edit'
  subCategoryForm.value = {
    id_sous_categorie: subCategory.id_sous_categorie,
    libelle_sous_categorie: subCategory.libelle_sous_categorie,
    id_categorie: subCategory.id_categorie
  }
  showSubCategoryModal.value = true
}

const closeSubCategoryModal = () => {
  showSubCategoryModal.value = false
  subCategoryForm.value = {
    id_sous_categorie: null,
    libelle_sous_categorie: '',
    id_categorie: null
  }
}

const saveSubCategory = async () => {
  savingSubCategory.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    const data = {
      libelle_sous_categorie: subCategoryForm.value.libelle_sous_categorie,
      id_categorie: subCategoryForm.value.id_categorie
    }

    if (subCategoryModalMode.value === 'create') {
      await subCategoriesAPI.createSubCategory(data)
      successMessage.value = 'Sous-catégorie créée avec succès'
    } else {
      await subCategoriesAPI.updateSubCategory(subCategoryForm.value.id_sous_categorie, { libelle_sous_categorie: data.libelle_sous_categorie })
      successMessage.value = 'Sous-catégorie mise à jour avec succès'
    }

    closeSubCategoryModal()
    await viewSubCategories(selectedCategory.value)
    setTimeout(() => successMessage.value = '', 3000)
  } catch (error) {
    console.error('Erreur lors de l\'enregistrement de la sous-catégorie:', error)
    const message = error.response?.data?.message || error.message || 'Erreur lors de l\'enregistrement de la sous-catégorie'
    errorMessage.value = message
    setTimeout(() => errorMessage.value = '', 5000)
  } finally {
    savingSubCategory.value = false
  }
}

const confirmDeleteCategory = (categorie) => {
  deleteTarget.value = { type: 'category', data: categorie }
  showDeleteModal.value = true
}

const confirmDeleteSubCategory = (subCategory) => {
  deleteTarget.value = { type: 'subCategory', data: subCategory }
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  deleteTarget.value = null
}

const executeDelete = async () => {
  deleting.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    if (deleteTarget.value.type === 'category') {
      await categoriesAPI.deleteCategory(deleteTarget.value.data.id_categorie)
      successMessage.value = 'Catégorie supprimée avec succès'
      await fetchCategories()
    } else {
      await subCategoriesAPI.deleteSubCategory(deleteTarget.value.data.id_sous_categorie)
      successMessage.value = 'Sous-catégorie supprimée avec succès'
      await viewSubCategories(selectedCategory.value)
    }

    closeDeleteModal()
    setTimeout(() => successMessage.value = '', 3000)
  } catch (error) {
    console.error('Erreur lors de la suppression:', error)
    const message = error.response?.data?.message || error.message || 'Erreur lors de la suppression'
    errorMessage.value = message
    setTimeout(() => errorMessage.value = '', 5000)
  } finally {
    deleting.value = false
  }
}

const getImageUrl = (imagePath) => {
  if (!imagePath) return '/images/placeholder.png'
  if (imagePath.startsWith('http')) return imagePath
  return `/${imagePath}`
}

const handleImageError = (event) => {
  event.target.src = '/images/placeholder.png'
}

// Lifecycle
onMounted(() => {
  fetchCategories()
})
</script>

<style scoped>
/* Custom styles if needed */
</style>

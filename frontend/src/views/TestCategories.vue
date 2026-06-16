<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Test API Catégories</h1>
    
    <button 
      @click="testAPI" 
      class="px-4 py-2 bg-blue-600 text-white rounded"
    >
      Tester l'API
    </button>
    
    <div v-if="loading" class="mt-4">Chargement...</div>
    
    <div v-if="result" class="mt-4 bg-gray-100 p-4 rounded">
      <h2 class="font-bold mb-2">Résultat:</h2>
      <pre class="text-sm overflow-auto">{{ JSON.stringify(result, null, 2) }}</pre>
    </div>
    
    <div v-if="error" class="mt-4 bg-red-100 p-4 rounded">
      <h2 class="font-bold mb-2 text-red-700">Erreur:</h2>
      <pre class="text-sm overflow-auto">{{ JSON.stringify(error, null, 2) }}</pre>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import * as categoriesAPI from '@/api/modules/admin/categories'

const loading = ref(false)
const result = ref(null)
const error = ref(null)

const testAPI = async () => {
  loading.value = true
  result.value = null
  error.value = null
  
  try {
    console.log('Appel de getCategories()...')
    const response = await categoriesAPI.getCategories()
    console.log('Réponse brute:', response)
    result.value = response
  } catch (err) {
    console.error('Erreur:', err)
    error.value = {
      message: err.message,
      response: err.response,
      full: err
    }
  } finally {
    loading.value = false
  }
}
</script>

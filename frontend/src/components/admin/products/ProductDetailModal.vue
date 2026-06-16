<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$emit('close')"></div>
      
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
      
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary-600 to-secondary-600 px-6 py-4">
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-semibold text-white" id="modal-title">
              Détails du Produit
            </h3>
            <button
              @click="$emit('close')"
              class="text-white hover:text-gray-200 transition-colors"
            >
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Content -->
        <div class="bg-white px-6 py-6 max-h-[calc(100vh-200px)] overflow-y-auto">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Images -->
            <div>
              <div class="space-y-4">
                <!-- Image principale -->
                <div class="relative rounded-lg overflow-hidden border-2 border-gray-200">
                  <img
                    v-if="product.image_produits"
                    :src="product.image_produits"
                    :alt="product.nom_produits"
                    class="w-full h-80 object-cover"
                  />
                  <div
                    v-else
                    class="w-full h-80 bg-gradient-to-br from-primary-400 to-secondary-400 flex items-center justify-center"
                  >
                    <span class="text-white text-6xl font-bold">
                      {{ product.nom_produits?.charAt(0).toUpperCase() }}
                    </span>
                  </div>
                </div>

                <!-- Images secondaires -->
                <div v-if="product.image_produits1 || product.image_produits2" class="grid grid-cols-2 gap-4">
                  <div v-if="product.image_produits1" class="rounded-lg overflow-hidden border border-gray-200">
                    <img
                      :src="product.image_produits1"
                      :alt="`${product.nom_produits} - Image 2`"
                      class="w-full h-40 object-cover"
                    />
                  </div>
                  <div v-if="product.image_produits2" class="rounded-lg overflow-hidden border border-gray-200">
                    <img
                      :src="product.image_produits2"
                      :alt="`${product.nom_produits} - Image 3`"
                      class="w-full h-40 object-cover"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- Informations -->
            <div class="space-y-6">
              <!-- Nom et prix -->
              <div>
                <h4 class="text-2xl font-bold text-gray-900 mb-2">{{ product.nom_produits }}</h4>
                <p class="text-3xl font-bold text-primary-600">{{ formatPrice(product.prix_produits) }} FCFA</p>
              </div>

              <!-- État -->
              <div>
                <label class="text-sm font-medium text-gray-500 block mb-1">État</label>
                <span
                  :class="{
                    'bg-warning-100 text-warning-800': product.state?.id_state === 1,
                    'bg-success-100 text-success-800': product.state?.id_state === 2,
                    'bg-error-100 text-error-800': product.state?.id_state === 3
                  }"
                  class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                >
                  {{ {'En Instance':'En attente','Publier':'Publié','Refuser':'Refusé'}[product.state?.title] ?? product.state?.title ?? 'Inconnu' }}
                </span>
              </div>

              <!-- Description -->
              <div>
                <label class="text-sm font-medium text-gray-500 block mb-1">Description</label>
                <p class="text-gray-700 text-sm leading-relaxed">{{ product.description_produits || 'Aucune description' }}</p>
              </div>

              <!-- Catégorie -->
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="text-sm font-medium text-gray-500 block mb-1">Catégorie</label>
                  <p class="text-gray-900 font-medium">{{ product.categorie?.nom_categorie || '-' }}</p>
                </div>
                <div>
                  <label class="text-sm font-medium text-gray-500 block mb-1">Sous-catégorie</label>
                  <p class="text-gray-900 font-medium">{{ product.sous_categorie?.libelle_sous_categorie || '-' }}</p>
                </div>
              </div>

              <!-- Client -->
              <div v-if="product.client">
                <label class="text-sm font-medium text-gray-500 block mb-1">Publié par</label>
                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                  <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center">
                    <span class="text-primary-600 font-semibold text-sm">
                      {{ product.client.nom?.charAt(0).toUpperCase() }}
                    </span>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900">{{ product.client.nom }}</p>
                    <p class="text-xs text-gray-500">{{ product.client.email }}</p>
                  </div>
                </div>
              </div>
              <div v-else>
                <label class="text-sm font-medium text-gray-500 block mb-1">Publié par</label>
                <p class="text-sm text-gray-500 italic">Administrateur</p>
              </div>

              <!-- Validé par -->
              <div v-if="product.validated_by">
                <label class="text-sm font-medium text-gray-500 block mb-1">Validé par</label>
                <p class="text-sm text-gray-900">{{ product.validated_by.nom }}</p>
              </div>

              <!-- Date de création -->
              <div v-if="product.dateSaisie">
                <label class="text-sm font-medium text-gray-500 block mb-1">Date de création</label>
                <p class="text-sm text-gray-900">{{ formatDate(product.dateSaisie) }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-4 flex justify-end">
          <button
            @click="$emit('close')"
            class="px-6 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-medium"
          >
            Fermer
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  show: {
    type: Boolean,
    required: true
  },
  product: {
    type: Object,
    required: true
  }
})

defineEmits(['close'])

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR').format(price)
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>

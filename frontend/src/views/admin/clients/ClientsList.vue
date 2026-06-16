<template>
  <div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Gestion des Clients</h1>
        <p class="text-sm text-gray-600 mt-1">Gérez les comptes clients de la plateforme</p>
      </div>
      <div class="text-sm text-gray-500">
        <span class="font-semibold">{{ clients.length }}</span> client{{ clients.length > 1 ? 's' : '' }}
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
      <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-100 rounded-full animate-pulse mb-4">
        <svg class="w-8 h-8 text-primary-600 animate-spin" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
      <p class="text-gray-500">Chargement des clients...</p>
    </div>

    <!-- Clients List -->
    <div v-else class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
      <!-- Empty State -->
      <div v-if="clients.length === 0" class="p-12 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
          <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Aucun client</h3>
        <p class="text-gray-500">Les clients apparaîtront ici après leur inscription</p>
      </div>

      <!-- Table -->
      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Client
              </th>
              <th class="hidden md:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Contact
              </th>
              <th class="hidden lg:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Jetons
              </th>
              <th class="hidden xl:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Activité
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Statut
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="client in clients" :key="client.id_client" class="hover:bg-gray-50 transition-colors">
              <!-- Client Info -->
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10">
                    <img 
                      v-if="client.image_client" 
                      :src="getClientImage(client.image_client)" 
                      :alt="`${client.client_prenom} ${client.client_nom}`"
                      class="h-10 w-10 rounded-full object-cover border-2 border-gray-200"
                      @error="handleImageError"
                    />
                    <div v-else class="h-10 w-10 rounded-full bg-gradient-to-br from-secondary-400 to-secondary-600 flex items-center justify-center text-white font-semibold text-sm">
                      {{ client.client_prenom?.[0] }}{{ client.client_nom?.[0] }}
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">
                      {{ client.client_prenom }} {{ client.client_nom }}
                    </div>
                    <div class="text-sm text-gray-500 md:hidden">
                      {{ client.client_email }}
                    </div>
                  </div>
                </div>
              </td>

              <!-- Contact -->
              <td class="hidden md:table-cell px-6 py-4">
                <div class="text-sm text-gray-900">{{ client.client_email }}</div>
                <div class="text-sm text-gray-500">{{ client.client_tel }}</div>
              </td>

              <!-- Jetons -->
              <td class="hidden lg:table-cell px-6 py-4 whitespace-nowrap">
                <div class="text-sm">
                  <div class="flex items-center text-gray-900">
                    <svg class="w-4 h-4 mr-1 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                    </svg>
                    {{ client.client_jettons || 0 }}
                  </div>
                </div>
              </td>

              <!-- Activité -->
              <td class="hidden xl:table-cell px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">
                  {{ client.produits_count || 0 }} produit{{ (client.produits_count || 0) > 1 ? 's' : '' }}
                </div>
                </td>

              <!-- Status -->
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  :class="[
                    'px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full',
                    client.client_email_status === 'vérifier' 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-yellow-100 text-yellow-800'
                  ]"
                >
                  {{ client.client_email_status === 'vérifier' ? '✓ Vérifié' : '⏳ En attente' }}
                </span>
              </td>

              <!-- Actions -->
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button
                  @click="viewClient(client)"
                  class="text-primary-600 hover:text-primary-900 mr-3"
                  title="Voir les détails"
                >
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>
                <button
                  @click="confirmDelete(client)"
                  class="text-error-600 hover:text-error-900"
                  title="Supprimer le client et toutes ses données"
                >
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Details Modal -->
    <div v-if="showDetailsModal" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showDetailsModal = false"></div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
            <div class="flex items-start justify-between mb-4">
              <h3 class="text-lg font-medium text-gray-900">Détails du client</h3>
              <button @click="showDetailsModal = false" class="text-gray-400 hover:text-gray-500">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <div v-if="selectedClient" class="space-y-4">
              <!-- Avatar et nom -->
              <div class="flex items-center space-x-4 pb-4 border-b">
                <div class="flex-shrink-0">
                  <img 
                    v-if="selectedClient.image_client" 
                    :src="getClientImage(selectedClient.image_client)" 
                    :alt="`${selectedClient.client_prenom} ${selectedClient.client_nom}`"
                    class="h-20 w-20 rounded-full object-cover border-2 border-gray-200"
                    @error="handleImageError"
                  />
                  <div v-else class="h-20 w-20 rounded-full bg-gradient-to-br from-secondary-400 to-secondary-600 flex items-center justify-center text-white font-semibold text-2xl">
                    {{ selectedClient.client_prenom?.[0] }}{{ selectedClient.client_nom?.[0] }}
                  </div>
                </div>
                <div>
                  <h4 class="text-xl font-semibold text-gray-900">
                    {{ selectedClient.client_prenom }} {{ selectedClient.client_nom }}
                  </h4>
                  <p class="text-sm text-gray-500">{{ selectedClient.client_login }}</p>
                </div>
              </div>

              <!-- Informations de contact -->
              <div>
                <h5 class="font-medium text-gray-900 mb-2">Informations de contact</h5>
                <dl class="grid grid-cols-1 gap-2 text-sm">
                  <div class="flex justify-between py-2 border-b">
                    <dt class="text-gray-600">Email :</dt>
                    <dd class="text-gray-900 font-medium">{{ selectedClient.client_email }}</dd>
                  </div>
                  <div class="flex justify-between py-2 border-b">
                    <dt class="text-gray-600">Téléphone :</dt>
                    <dd class="text-gray-900 font-medium">{{ selectedClient.client_tel || 'Non renseigné' }}</dd>
                  </div>
                  <div class="flex justify-between py-2 border-b">
                    <dt class="text-gray-600">Adresse :</dt>
                    <dd class="text-gray-900 font-medium text-right">{{ selectedClient.client_adresse || 'Non renseignée' }}</dd>
                  </div>
                </dl>
              </div>

              <!-- Jetons -->
              <div>
                <h5 class="font-medium text-gray-900 mb-2">Jetons</h5>
                <div class="grid grid-cols-2 gap-4">
                  <div class="bg-yellow-50 rounded-lg p-3">
                    <div class="flex items-center justify-between">
                      <span class="text-sm text-gray-600">Jetons produits</span>
                      <span class="text-2xl font-bold text-yellow-600">{{ selectedClient.client_jettons || 0 }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Activité -->
              <div>
                <h5 class="font-medium text-gray-900 mb-2">Activité</h5>
                <div class="grid grid-cols-2 gap-4">
                  <div class="bg-blue-50 rounded-lg p-3">
                    <div class="flex items-center justify-between">
                      <span class="text-sm text-gray-600">Produits</span>
                      <span class="text-2xl font-bold text-blue-600">{{ selectedClient.produits_count || 0 }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Statut -->
              <div>
                <h5 class="font-medium text-gray-900 mb-2">Statut du compte</h5>
                <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg">
                  <span class="text-sm text-gray-600">Vérification email :</span>
                  <span 
                    :class="[
                      'px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full',
                      selectedClient.client_email_status === 'vérifier' 
                        ? 'bg-green-100 text-green-800' 
                        : 'bg-yellow-100 text-yellow-800'
                    ]"
                  >
                    {{ selectedClient.client_email_status === 'vérifier' ? '✓ Vérifié' : '⏳ En attente' }}
                  </span>
                </div>
                <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg mt-2">
                  <span class="text-sm text-gray-600">Étape d'inscription :</span>
                  <span class="text-sm font-medium text-gray-900">{{ selectedClient.etape || 'Non définie' }}</span>
                </div>
              </div>
            </div>

            <div class="mt-6 flex justify-end">
              <button
                @click="showDetailsModal = false"
                type="button"
                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
              >
                Fermer
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="cancelDelete"></div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-error-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-error-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                  Supprimer le client
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Êtes-vous sûr de vouloir supprimer <strong>{{ clientToDelete?.client_prenom }} {{ clientToDelete?.client_nom }}</strong> ?
                  </p>
                  <div class="mt-3 bg-error-50 border-l-4 border-error-500 p-3 rounded">
                    <p class="text-sm text-error-700 font-semibold">Attention : Cette action est irréversible !</p>
                    <p class="text-xs text-error-600 mt-2">
                      Seront également supprimés :
                    </p>
                    <ul class="text-xs text-error-600 mt-1 ml-4 list-disc">
                      <li>{{ clientToDelete?.produits_count || 0 }} produit(s)</li>

                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              @click="deleteClient"
              :disabled="deleting"
              type="button"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-error-600 text-base font-medium text-white hover:bg-error-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-error-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="deleting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ deleting ? 'Suppression...' : 'Supprimer définitivement' }}
            </button>
            <button
              @click="cancelDelete"
              :disabled="deleting"
              type="button"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Annuler
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { clientsAPI } from '@/api/modules/admin/clients'

const clients = ref([])
const loading = ref(false)
const showDeleteModal = ref(false)
const showDetailsModal = ref(false)
const clientToDelete = ref(null)
const selectedClient = ref(null)
const deleting = ref(false)

// Charger les clients
const loadClients = async () => {
  loading.value = true
  try {
    const response = await clientsAPI.getAll()
    // Plus flexible: accepte la réponse si elle contient des données
    if (response.data && Array.isArray(response.data)) {
      clients.value = response.data
      console.log(`${response.data.length} clients chargés`)
    } else if (response.message) {
      console.warn('Réponse API:', response.message)
    }
  } catch (error) {
    console.error('Erreur lors du chargement des clients:', error)
    console.error('Détails:', error.response?.data)
  } finally {
    loading.value = false
  }
}

// Voir les détails d'un client
const viewClient = (client) => {
  selectedClient.value = client
  showDetailsModal.value = true
}

// Confirmer la suppression
const confirmDelete = (client) => {
  clientToDelete.value = client
  showDeleteModal.value = true
}

// Annuler la suppression
const cancelDelete = () => {
  showDeleteModal.value = false
  clientToDelete.value = null
}

// Supprimer le client
const deleteClient = async () => {
  if (!clientToDelete.value) return
  
  deleting.value = true
  try {
    const response = await clientsAPI.delete(clientToDelete.value.id_client)
    // Plus flexible: vérifier si la suppression a réussi
    if (response.message || response.success !== false) {
      // Retirer de la liste
      clients.value = clients.value.filter(c => c.id_client !== clientToDelete.value.id_client)
      showDeleteModal.value = false
      clientToDelete.value = null
      console.log('Client supprimé avec succès')
    }
  } catch (error) {
    console.error('Erreur lors de la suppression du client:', error)
    alert('Erreur lors de la suppression du client. Vérifiez la console.')
  } finally {
    deleting.value = false
  }
}

// Gestion des images
const getClientImage = (imagePath) => {
  if (!imagePath) return ''
  if (imagePath.startsWith('http')) return imagePath
  return `${import.meta.env.VITE_API_URL}/storage/${imagePath}`
}

const handleImageError = (event) => {
  event.target.style.display = 'none'
}

// Chargement initial
onMounted(() => {
  loadClients()
})
</script>

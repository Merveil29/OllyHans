<template>
  <div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Gestion des Administrateurs</h1>
        <p class="text-sm text-gray-600 mt-1">Gérez les comptes administrateurs de la plateforme</p>
      </div>
      <button
        @click="showCreateModal = true"
        class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors shadow-sm"
      >
        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Nouvel Administrateur
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
      <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-100 rounded-full animate-pulse mb-4">
        <svg class="w-8 h-8 text-primary-600 animate-spin" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
      <p class="text-gray-500">Chargement des administrateurs...</p>
    </div>

    <!-- Admins List -->
    <div v-else class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
      <!-- Empty State -->
      <div v-if="admins.length === 0" class="p-12 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
          <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Aucun administrateur</h3>
        <p class="text-gray-500">Créez votre premier compte administrateur</p>
      </div>

      <!-- Table -->
      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Administrateur
              </th>
              <th class="hidden md:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Contact
              </th>
              <th class="hidden lg:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Login
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
            <tr v-for="admin in admins" :key="admin.id_user" class="hover:bg-gray-50 transition-colors">
              <!-- Admin Info -->
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10">
                    <img 
                      v-if="admin.image_user" 
                      :src="admin.image_user" 
                      :alt="`${admin.user_prenom} ${admin.user_nom}`"
                      class="h-10 w-10 rounded-full object-cover border-2 border-gray-200"
                    />
                    <div v-else class="h-10 w-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-semibold text-sm">
                      {{ admin.user_prenom?.[0] }}{{ admin.user_nom?.[0] }}
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">
                      {{ admin.user_prenom }} {{ admin.user_nom }}
                    </div>
                    <div class="text-sm text-gray-500 md:hidden">
                      {{ admin.user_email }}
                    </div>
                  </div>
                </div>
              </td>

              <!-- Contact -->
              <td class="hidden md:table-cell px-6 py-4">
                <div class="text-sm text-gray-900">{{ admin.user_email }}</div>
                <div class="text-sm text-gray-500">{{ admin.user_tel }}</div>
              </td>

              <!-- Login -->
              <td class="hidden lg:table-cell px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ admin.user_login }}</div>
              </td>

              <!-- Status -->
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  :class="[
                    'px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full',
                    admin.user_email_status === 'vérifier' 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-yellow-100 text-yellow-800'
                  ]"
                >
                  {{ admin.user_email_status === 'vérifier' ? '✓ Vérifié' : '⏳ En attente' }}
                </span>
              </td>

              <!-- Actions -->
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button
                  @click="viewAdminDetails(admin.id_user)"
                  class="text-primary-600 hover:text-primary-900 mr-3"
                  title="Voir les détails"
                >
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>
                <button
                  @click="confirmDelete(admin)"
                  :disabled="admin.id_user === currentUserId"
                  :class="[
                    'text-error-600 hover:text-error-900',
                    admin.id_user === currentUserId ? 'opacity-50 cursor-not-allowed' : ''
                  ]"
                  :title="admin.id_user === currentUserId ? 'Vous ne pouvez pas supprimer votre propre compte' : 'Supprimer'"
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

    <!-- Create Admin Modal -->
    <Teleport to="body">
      <div
        v-if="showCreateModal"
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
      >
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <!-- Overlay -->
          <div
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            @click="closeCreateModal"
          ></div>

          <!-- Modal Content -->
          <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form @submit.prevent="createAdmin">
              <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                  <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-primary-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                  </div>
                  <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                      Créer un administrateur
                    </h3>

                    <!-- Error Message -->
                    <div v-if="createError" class="mb-4 bg-error-50 border-l-4 border-error-500 p-3 rounded">
                      <p class="text-sm text-error-700">{{ createError }}</p>
                    </div>

                    <!-- Success Message -->
                    <div v-if="createSuccess" class="mb-4 bg-success-50 border-l-4 border-success-500 p-3 rounded">
                      <p class="text-sm text-success-700">{{ createSuccess }}</p>
                      <p class="text-xs text-success-600 mt-2">
                        Un email avec les identifiants de connexion a été envoyé à l'administrateur.
                      </p>
                    </div>

                    <!-- Form Fields -->
                    <div class="space-y-4">
                      <div class="grid grid-cols-2 gap-4">
                        <div>
                          <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                          <input
                            v-model="newAdmin.user_nom"
                            type="text"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                          />
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700 mb-1">Prénom *</label>
                          <input
                            v-model="newAdmin.user_prenom"
                            type="text"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                          />
                        </div>
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input
                          v-model="newAdmin.user_email"
                          type="email"
                          required
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Login *</label>
                        <input
                          v-model="newAdmin.user_login"
                          type="text"
                          required
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        />
                      </div>

                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
                        <input
                          v-model="newAdmin.user_tel"
                          type="tel"
                          required
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                <button
                  type="submit"
                  :disabled="creating"
                  class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                >
                  <svg v-if="creating" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ creating ? 'Création...' : 'Créer' }}
                </button>
                <button
                  type="button"
                  @click="closeCreateModal"
                  :disabled="creating"
                  class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:w-auto sm:text-sm"
                >
                  Annuler
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- View Details Modal -->
    <Teleport to="body">
      <div
        v-if="showDetailsModal"
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
      >
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <!-- Overlay -->
          <div
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            @click="showDetailsModal = false"
          ></div>

          <!-- Modal Content -->
          <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div v-if="loadingDetails" class="p-12 text-center">
              <svg class="animate-spin h-8 w-8 mx-auto text-primary-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <p class="text-gray-500 mt-4">Chargement...</p>
            </div>

            <div v-else-if="selectedAdmin" class="bg-white px-4 pt-5 pb-4 sm:p-6">
              <!-- Profile Photo -->
              <div class="flex justify-center mb-6">
                <div class="relative">
                  <img 
                    v-if="selectedAdmin.image" 
                    :src="selectedAdmin.image" 
                    :alt="`${selectedAdmin.prenom} ${selectedAdmin.nom}`"
                    class="h-24 w-24 rounded-full object-cover border-4 border-primary-100 shadow-lg"
                  />
                  <div v-else class="h-24 w-24 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-bold text-3xl border-4 border-primary-100 shadow-lg">
                    {{ selectedAdmin.prenom?.[0] }}{{ selectedAdmin.nom?.[0] }}
                  </div>
                  <div class="absolute bottom-0 right-0 h-6 w-6 rounded-full border-2 border-white" :class="selectedAdmin.email_status === 'vérifier' ? 'bg-green-500' : 'bg-yellow-500'"></div>
                </div>
              </div>

              <!-- Admin Details -->
              <div class="space-y-4">
                <div class="text-center mb-6">
                  <h3 class="text-xl font-bold text-gray-900">{{ selectedAdmin.prenom }} {{ selectedAdmin.nom }}</h3>
                  <span 
                    :class="[
                      'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold mt-2',
                      selectedAdmin.email_status === 'vérifier' 
                        ? 'bg-green-100 text-green-800' 
                        : 'bg-yellow-100 text-yellow-800'
                    ]"
                  >
                    {{ selectedAdmin.email_status === 'vérifier' ? '✓ Compte vérifié' : '⏳ En attente de vérification' }}
                  </span>
                </div>

                <div class="border-t border-gray-200 pt-4">
                  <dl class="space-y-3">
                    <div class="flex justify-between">
                      <dt class="text-sm font-medium text-gray-500">Email</dt>
                      <dd class="text-sm text-gray-900">{{ selectedAdmin.email }}</dd>
                    </div>
                    <div class="flex justify-between">
                      <dt class="text-sm font-medium text-gray-500">Login</dt>
                      <dd class="text-sm text-gray-900">{{ selectedAdmin.login }}</dd>
                    </div>
                    <div class="flex justify-between">
                      <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                      <dd class="text-sm text-gray-900">{{ selectedAdmin.telephone }}</dd>
                    </div>
                    <div class="flex justify-between">
                      <dt class="text-sm font-medium text-gray-500">ID</dt>
                      <dd class="text-sm text-gray-900">#{{ selectedAdmin.id }}</dd>
                    </div>
                  </dl>
                </div>
              </div>

              <div class="mt-6 flex justify-end">
                <button
                  @click="showDetailsModal = false"
                  class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
                >
                  Fermer
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Delete Confirmation Modal -->
    <Teleport to="body">
      <div
        v-if="showDeleteModal"
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
      >
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <!-- Overlay -->
          <div
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            @click="showDeleteModal = false"
          ></div>

          <!-- Modal Content -->
          <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-error-100 sm:mx-0 sm:h-10 sm:w-10">
                  <svg class="h-6 w-6 text-error-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                  <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                    Supprimer l'administrateur
                  </h3>
                  <div class="mt-2">
                    <p class="text-sm text-gray-500">
                      Êtes-vous sûr de vouloir supprimer <strong>{{ adminToDelete?.user_prenom }} {{ adminToDelete?.user_nom }}</strong> ?
                      Cette action est irréversible.
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
              <button
                type="button"
                @click="deleteAdmin"
                :disabled="deleting"
                class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-error-600 text-base font-medium text-white hover:bg-error-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-error-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
              >
                <svg v-if="deleting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ deleting ? 'Suppression...' : 'Supprimer' }}
              </button>
              <button
                type="button"
                @click="showDeleteModal = false"
                :disabled="deleting"
                class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:w-auto sm:text-sm"
              >
                Annuler
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { getAdminUsers, getAdminUser, createAdminUser, deleteAdminUser } from '@/api/modules/admin/users'

const authStore = useAuthStore()

const admins = ref([])
const loading = ref(true)
const showCreateModal = ref(false)
const showDetailsModal = ref(false)
const showDeleteModal = ref(false)
const creating = ref(false)
const deleting = ref(false)
const loadingDetails = ref(false)
const createError = ref('')
const createSuccess = ref('')
const selectedAdmin = ref(null)
const adminToDelete = ref(null)

const newAdmin = ref({
  user_nom: '',
  user_prenom: '',
  user_email: '',
  user_login: '',
  user_tel: ''
})

const currentUserId = computed(() => authStore.user?.id_user)

// Charger les administrateurs
const loadAdmins = async () => {
  loading.value = true
  try {
    const response = await getAdminUsers()
    admins.value = response.data
  } catch (error) {
    console.error('Erreur lors du chargement des admins:', error)
  } finally {
    loading.value = false
  }
}

// Créer un administrateur
const createAdmin = async () => {
  createError.value = ''
  createSuccess.value = ''
  creating.value = true

  try {
    const response = await createAdminUser(newAdmin.value)
    
    createSuccess.value = response.message || 'Administrateur créé avec succès !'
    
    // Recharger la liste
    await loadAdmins()

    // Réinitialiser le formulaire après 2 secondes
    setTimeout(() => {
      closeCreateModal()
    }, 3000)

  } catch (error) {
    console.error('Erreur lors de la création:', error)
    createError.value = error.response?.data?.message || 'Une erreur est survenue'
    
    // Afficher les erreurs de validation
    if (error.response?.data?.errors) {
      const errors = error.response.data.errors
      createError.value = Object.values(errors).flat().join(', ')
    }
  } finally {
    creating.value = false
  }
}

// Fermer le modal de création
const closeCreateModal = () => {
  showCreateModal.value = false
  newAdmin.value = {
    user_nom: '',
    user_prenom: '',
    user_email: '',
    user_login: '',
    user_tel: ''
  }
  createError.value = ''
  createSuccess.value = ''
}

// Voir les détails d'un admin
const viewAdminDetails = async (id) => {
  showDetailsModal.value = true
  loadingDetails.value = true
  selectedAdmin.value = null

  try {
    const response = await getAdminUser(id)
    selectedAdmin.value = response.data
  } catch (error) {
    console.error('Erreur lors du chargement des détails:', error)
    showDetailsModal.value = false
  } finally {
    loadingDetails.value = false
  }
}

// Confirmer la suppression
const confirmDelete = (admin) => {
  adminToDelete.value = admin
  showDeleteModal.value = true
}

// Supprimer un admin
const deleteAdmin = async () => {
  if (!adminToDelete.value) return

  deleting.value = true

  try {
    await deleteAdminUser(adminToDelete.value.id_user)
    
    // Recharger la liste
    await loadAdmins()
    
    showDeleteModal.value = false
    adminToDelete.value = null
  } catch (error) {
    console.error('Erreur lors de la suppression:', error)
    alert(error.response?.data?.message || 'Erreur lors de la suppression')
  } finally {
    deleting.value = false
  }
}

onMounted(() => {
  loadAdmins()
})
</script>

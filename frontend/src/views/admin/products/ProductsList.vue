<template>
  <div class="space-y-6">
    <!-- Header avec titre -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Gestion des Produits</h1>
        <p class="text-sm text-gray-600 mt-1">Gérez tous les produits de la plateforme</p>
      </div>
      <router-link
        to="/admin/products/create"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium transition-colors"
      >
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nouveau produit
      </router-link>
    </div>

    <!-- Filtres et recherche -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Recherche -->
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input
              v-model="filters.search"
              type="text"
              placeholder="Rechercher par nom ou description..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              @input="debouncedSearch"
            />
          </div>
        </div>

        <!-- Filtre par état -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">État</label>
          <select
            v-model="filters.state"
            @change="loadProducts"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
          >
            <option value="">Tous les états</option>
            <option value="1">En attente</option>
            <option value="2">Validé</option>
            <option value="3">Rejeté</option>
          </select>
        </div>

        <!-- Filtre par catégorie -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
          <select
            v-model="filters.categorie"
            @change="loadProducts"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
          >
            <option value="">Toutes</option>
            <option v-for="cat in categories" :key="cat.id_categorie" :value="cat.id_categorie">
              {{ cat.nom_categorie }}
            </option>
          </select>
        </div>
      </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">Total</p>
            <p class="text-2xl font-bold text-primary-600">{{ stats.total }}</p>
          </div>
          <div class="p-3 bg-primary-100 rounded-lg">
            <svg class="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">En attente</p>
            <p class="text-2xl font-bold text-warning-600">{{ stats.pending }}</p>
          </div>
          <div class="p-3 bg-warning-100 rounded-lg">
            <svg class="w-6 h-6 text-warning-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">Validés</p>
            <p class="text-2xl font-bold text-success-600">{{ stats.validated }}</p>
          </div>
          <div class="p-3 bg-success-100 rounded-lg">
            <svg class="w-6 h-6 text-success-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">Rejetés</p>
            <p class="text-2xl font-bold text-error-600">{{ stats.rejected }}</p>
          </div>
          <div class="p-3 bg-error-100 rounded-lg">
            <svg class="w-6 h-6 text-error-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Liste des produits -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
      <!-- Loading -->
      <div v-if="loading" class="p-12 text-center">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-primary-200 border-t-primary-600"></div>
        <p class="mt-4 text-gray-600">Chargement des produits...</p>
      </div>

      <!-- Empty state -->
      <div v-else-if="!products.length" class="p-12 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
          <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
          </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun produit trouvé</h3>
        <p class="text-gray-500 mb-4">Aucun produit ne correspond à vos critères de recherche</p>
        <button
          @click="resetFilters"
          class="text-primary-600 hover:text-primary-700 font-medium"
        >
          Réinitialiser les filtres
        </button>
      </div>

      <!-- Table responsive -->
      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Produit
              </th>
              <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Prix
              </th>
              <th class="hidden lg:table-cell px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Catégorie
              </th>
              <th class="hidden lg:table-cell px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Client
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                État
              </th>
              <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="product in products" :key="product.id_produits" class="hover:bg-gray-50">
              <!-- Produit info -->
              <td class="px-4 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-12 w-12">
                    <img
                      v-if="product.image_produits"
                      :src="getProductImage(product.image_produits)"
                      :alt="product.nom_produits"
                      class="h-12 w-12 rounded-lg object-cover border border-gray-200"
                    />
                    <div
                      v-else
                      class="h-12 w-12 rounded-lg bg-gradient-to-br from-primary-400 to-secondary-400 flex items-center justify-center"
                    >
                      <span class="text-white font-semibold text-lg">
                        {{ product.nom_produits.charAt(0).toUpperCase() }}
                      </span>
                    </div>
                  </div>
                  <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 line-clamp-1">{{ product.nom_produits }}</p>
                    <p class="text-xs text-gray-500 line-clamp-1 md:hidden">{{ formatPrice(product.prix_produits) }} FCFA</p>
                  </div>
                </div>
              </td>

              <!-- Prix (hidden on mobile) -->
              <td class="hidden md:table-cell px-4 py-4 whitespace-nowrap">
                <div class="text-sm font-semibold text-primary-600">{{ formatPrice(product.prix_produits) }} FCFA</div>
              </td>

              <!-- Catégorie (hidden on tablet) -->
              <td class="hidden lg:table-cell px-4 py-4">
                <div class="text-sm">
                  <p class="text-gray-900">{{ product.categorie?.nom_categorie || '-' }}</p>
                  <p class="text-gray-500 text-xs">{{ product.sous_categorie?.nom_sous_categorie || '-' }}</p>
                </div>
              </td>

              <!-- Client (hidden on tablet) -->
              <td class="hidden lg:table-cell px-4 py-4">
                <div v-if="product.client" class="text-sm">
                  <p class="text-gray-900">{{ product.client.nom }}</p>
                  <p class="text-gray-500 text-xs">{{ product.client.email }}</p>
                </div>
                <span v-else class="text-sm text-gray-400 italic">Admin</span>
              </td>

              <!-- État -->
              <td class="px-4 py-4 whitespace-nowrap">
                <span
                  :class="{
                    'bg-warning-100 text-warning-800': product.state?.id_state === 1,
                    'bg-success-100 text-success-800': product.state?.id_state === 2,
                    'bg-error-100 text-error-800': product.state?.id_state === 3,
                    'bg-gray-100 text-gray-800': !product.state
                  }"
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                >
                  {{ {'En Instance':'En attente','Publier':'Publié','Refuser':'Refusé'}[product.state?.title] ?? product.state?.title ?? 'Inconnu' }}
                </span>
              </td>

              <!-- Actions -->
              <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex items-center justify-end gap-2">
                  <!-- View -->
                  <button
                    @click="handleView(product)"
                    class="p-2 text-primary-600 hover:bg-primary-50 rounded-lg transition-colors"
                    title="Voir détails"
                  >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>

                  <!-- Validate -->
                  <button
                    v-if="product.state?.id_state !== 2"
                    @click="handleValidate(product)"
                    :disabled="actionLoading"
                    class="p-2 text-success-600 hover:bg-success-50 rounded-lg transition-colors disabled:opacity-50"
                    title="Valider"
                  >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </button>

                  <!-- Reject -->
                  <button
                    v-if="product.state?.id_state !== 3"
                    @click="handleReject(product)"
                    :disabled="actionLoading"
                    class="p-2 text-error-600 hover:bg-error-50 rounded-lg transition-colors disabled:opacity-50"
                    title="Rejeter"
                  >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </button>

                  <!-- Delete -->
                  <button
                    @click="handleDelete(product)"
                    :disabled="actionLoading"
                    class="p-2 text-error-600 hover:bg-error-50 rounded-lg transition-colors disabled:opacity-50"
                    title="Supprimer"
                  >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.total > pagination.per_page" class="px-4 py-3 bg-gray-50 border-t border-gray-200">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
          <div class="text-sm text-gray-700">
            Affichage de <span class="font-medium">{{ (pagination.current_page - 1) * pagination.per_page + 1 }}</span>
            à <span class="font-medium">{{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }}</span>
            sur <span class="font-medium">{{ pagination.total }}</span> résultats
          </div>
          
          <div class="flex gap-2">
            <button
              @click="loadPage(pagination.current_page - 1)"
              :disabled="pagination.current_page === 1"
              class="px-3 py-1 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Précédent
            </button>
            <button
              @click="loadPage(pagination.current_page + 1)"
              :disabled="pagination.current_page === pagination.last_page"
              class="px-3 py-1 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Suivant
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Validation -->
    <div v-if="showValidateModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModals"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-success-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-success-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                  Valider le produit
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Êtes-vous sûr de vouloir valider le produit <span class="font-semibold text-gray-900">"{{ selectedProduct?.nom_produits }}"</span> ?
                  </p>
                  <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Commentaire (optionnel)
                    </label>
                    <p class="text-xs text-gray-500 mb-2">
                      📧 Un email sera envoyé au client uniquement si vous ajoutez un commentaire.
                    </p>
                    <textarea
                      v-model="validationComment"
                      rows="3"
                      placeholder="Ajoutez un commentaire pour envoyer un email au client..."
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-success-500 focus:border-transparent"
                    ></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
            <button
              type="button"
              @click="confirmValidate"
              :disabled="actionLoading"
              class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-success-600 text-base font-medium text-white hover:bg-success-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-success-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
            >
              {{ actionLoading ? 'Validation...' : 'Valider' }}
            </button>
            <button
              type="button"
              @click="closeModals"
              :disabled="actionLoading"
              class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:w-auto sm:text-sm disabled:opacity-50"
            >
              Annuler
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Rejet -->
    <div v-if="showRejectModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModals"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-error-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-error-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                  Rejeter le produit
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Êtes-vous sûr de vouloir rejeter le produit <span class="font-semibold text-gray-900">"{{ selectedProduct?.nom_produits }}"</span> ?
                  </p>
                  <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Raison du rejet <span class="text-error-600">*</span>
                    </label>
                    <p class="text-xs text-gray-500 mb-2">
                      📧 Un email sera automatiquement envoyé au client avec votre explication.
                    </p>
                    <textarea
                      v-model="rejectReason"
                      rows="3"
                      placeholder="Expliquez pourquoi ce produit est rejeté (min. 10 caractères)..."
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-error-500 focus:border-transparent"
                      :class="{ 'border-error-500': rejectReasonError }"
                    ></textarea>
                    <p v-if="rejectReasonError" class="mt-1 text-sm text-error-600">{{ rejectReasonError }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
            <button
              type="button"
              @click="confirmReject"
              :disabled="actionLoading"
              class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-error-600 text-base font-medium text-white hover:bg-error-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-error-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
            >
              {{ actionLoading ? 'Rejet...' : 'Rejeter' }}
            </button>
            <button
              type="button"
              @click="closeModals"
              :disabled="actionLoading"
              class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:w-auto sm:text-sm disabled:opacity-50"
            >
              Annuler
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Suppression -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModals"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
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
                  Supprimer le produit
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Êtes-vous sûr de vouloir supprimer définitivement le produit <span class="font-semibold text-gray-900">"{{ selectedProduct?.nom_produits }}"</span> ?
                  </p>
                  <p class="text-sm text-error-600 font-medium mt-2">
                    ⚠️ Cette action est irréversible !
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
            <button
              type="button"
              @click="confirmDelete"
              :disabled="actionLoading"
              class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-error-600 text-base font-medium text-white hover:bg-error-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-error-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
            >
              {{ actionLoading ? 'Suppression...' : 'Supprimer' }}
            </button>
            <button
              type="button"
              @click="closeModals"
              :disabled="actionLoading"
              class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:w-auto sm:text-sm disabled:opacity-50"
            >
              Annuler
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de détails -->
  <ProductDetailModal
    :show="showDetailModal"
    :product="selectedProduct || {}"
    @close="closeModals"
  />
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { getProducts, getProductStats, validateProduct, rejectProduct, deleteProduct } from '@/api/modules/admin/products'
import { getCategories } from '@/api/modules/admin/categories'
import ProductDetailModal from '@/components/admin/products/ProductDetailModal.vue'

// State
const products = ref([])
const categories = ref([])
const loading = ref(false)
const actionLoading = ref(null)

// Modal states
const showDetailModal = ref(false)
const showValidateModal = ref(false)
const showRejectModal = ref(false)
const showDeleteModal = ref(false)
const selectedProduct = ref(null)
const validationComment = ref('')
const rejectReason = ref('')
const rejectReasonError = ref('')

const filters = ref({
  search: '',
  state: '',
  categorie: ''
})

const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0
})

const stats = ref({
  total: 0,
  pending: 0,
  validated: 0,
  rejected: 0
})

// Methods
const loadCategories = async () => {
  try {
    const response = await getCategories()
    if (response.success && response.data) {
      categories.value = Array.isArray(response.data) ? response.data : []
    }
  } catch (error) {
    console.error('Erreur chargement catégories:', error)
  }
}

const loadStats = async () => {
  try {
    const response = await getProductStats()
    if (response.success && response.data) {
      stats.value = response.data
    }
  } catch (error) {
    console.error('Erreur chargement stats:', error)
  }
}

const loadProducts = async () => {
  try {
    loading.value = true
    const params = {
      page: pagination.value.current_page,
      per_page: pagination.value.per_page
    }
    
    if (filters.value.state) params.state = filters.value.state
    if (filters.value.search) params.search = filters.value.search
    if (filters.value.categorie) params.categorie = filters.value.categorie

    const response = await getProducts(params)
    
    if (response.success && response.data) {
      products.value = Array.isArray(response.data) ? response.data : []
      pagination.value.total = response.total || products.value.length
    }
    
    await loadStats()
  } catch (error) {
    console.error('Erreur chargement produits:', error)
  } finally {
    loading.value = false
  }
}

const loadPage = (page) => {
  pagination.value.current_page = page
  loadProducts()
}

let searchTimeout = null
const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    pagination.value.current_page = 1
    loadProducts()
  }, 300)
}

const resetFilters = () => {
  filters.value.search = ''
  filters.value.state = ''
  filters.value.categorie = ''
  pagination.value.current_page = 1
  loadProducts()
}

const getProductImage = (imagePath) => {
  if (!imagePath) return ''
  if (imagePath.startsWith('http')) return imagePath
  return `${import.meta.env.VITE_API_URL}/storage/${imagePath}`
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR').format(price)
}

// Modal functions
const closeModals = () => {
  showDetailModal.value = false
  showValidateModal.value = false
  showRejectModal.value = false
  showDeleteModal.value = false
  selectedProduct.value = null
  validationComment.value = ''
  rejectReason.value = ''
  rejectReasonError.value = ''
}

const handleView = (product) => {
  selectedProduct.value = product
  showDetailModal.value = true
}

const handleValidate = (product) => {
  selectedProduct.value = product
  validationComment.value = ''
  showValidateModal.value = true
}

const confirmValidate = async () => {
  if (!selectedProduct.value) return

  try {
    actionLoading.value = true
    const payload = validationComment.value ? { comment: validationComment.value } : {}
    await validateProduct(selectedProduct.value.id_produits, payload)
    await loadProducts()
    closeModals()
  } catch (error) {
    console.error('Erreur validation produit:', error)
    alert('Erreur lors de la validation du produit')
  } finally {
    actionLoading.value = false
  }
}

const handleReject = (product) => {
  selectedProduct.value = product
  rejectReason.value = ''
  rejectReasonError.value = ''
  showRejectModal.value = true
}

const confirmReject = async () => {
  if (!selectedProduct.value) return
  
  rejectReasonError.value = ''
  
  if (!rejectReason.value.trim()) {
    rejectReasonError.value = 'La raison du rejet est obligatoire'
    return
  }
  
  if (rejectReason.value.trim().length < 10) {
    rejectReasonError.value = 'La raison doit contenir au moins 10 caractères'
    return
  }

  try {
    actionLoading.value = true
    await rejectProduct(selectedProduct.value.id_produits, { reason: rejectReason.value })
    await loadProducts()
    closeModals()
  } catch (error) {
    console.error('Erreur rejet produit:', error)
    alert('Erreur lors du rejet du produit')
  } finally {
    actionLoading.value = false
  }
}

const handleDelete = (product) => {
  selectedProduct.value = product
  showDeleteModal.value = true
}

const confirmDelete = async () => {
  if (!selectedProduct.value) return

  try {
    actionLoading.value = true
    await deleteProduct(selectedProduct.value.id_produits)
    await loadProducts()
    closeModals()
  } catch (error) {
    console.error('Erreur suppression produit:', error)
    alert('Erreur lors de la suppression du produit')
  } finally {
    actionLoading.value = false
  }
}

// Lifecycle
onMounted(() => {
  loadCategories()
  loadProducts()
})
</script>

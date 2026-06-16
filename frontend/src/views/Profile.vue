<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-primary-50">
        <DashboardHeader />
   
    <!-- Header/Breadcrumb -->
    <!-- <div class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Mon Profil</h1>
              <p class="mt-1 text-sm text-gray-500">
                Gérez vos informations personnelles et vos paramètres
              </p>
            </div>
            <router-link 
              to="/" 
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
            >
              <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              Retour
            </router-link>
          </div>
        </div>
      </div>
    </div> -->

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Alert Messages -->
      <div v-if="successMessage" class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-green-700">{{ successMessage }}</p>
          </div>
        </div>
      </div>

      <div v-if="errorMessage" class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-red-700">{{ errorMessage }}</p>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Profile Photo -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Photo de profil</h3>
              
              <!-- Profile Photo Display -->
              <div class="flex flex-col items-center">
                <div class="relative group">
                  <img 
                    :src="previewImage || profileImage" 
                    alt="Photo de profil"
                    class="w-40 h-40 rounded-full object-cover border-4 border-primary-100 shadow-lg"
                  />
                  <div v-if="uploadingImage" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 rounded-full">
                    <svg class="animate-spin h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                  </div>
                </div>

                <p class="mt-4 text-center text-sm text-gray-600">
                  Bienvenue <span class="font-semibold text-gray-900">{{ userProfile?.prenom }} {{ userProfile?.nom }}</span>
                </p>

                <!-- File Input -->
                <div class="mt-6 w-full">
                  <label class="block">
                    <input 
                      ref="fileInput"
                      type="file" 
                      accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                      @change="handleFileSelect"
                      class="hidden"
                    />
                    <button
                      @click="$refs.fileInput.click()"
                      :disabled="uploadingImage"
                      class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50"
                    >
                      <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                      Modifier la photo
                    </button>
                  </label>

                  <button
                    v-if="selectedFile"
                    @click="uploadProfileImage"
                    :disabled="uploadingImage"
                    class="mt-2 w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50"
                  >
                    <svg v-if="!uploadingImage" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ uploadingImage ? 'Upload en cours...' : 'Sauvegarder' }}
                  </button>

                  <button
                    v-if="userProfile?.image && userProfile.image !== 'public/avatar.png' && !selectedFile"
                    @click="deleteProfileImage"
                    class="mt-2 w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                  >
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Supprimer la photo
                  </button>

                  <p class="mt-2 text-xs text-gray-500">
                    JPG, PNG, GIF ou WEBP (Max. 5MB)
                  </p>
                </div>
              </div>
            </div>

            <!-- Account Status Card -->
            <div class="border-t border-gray-200 p-6">
              <h3 class="text-sm font-medium text-gray-900 mb-3">Statut du compte</h3>
              <div class="flex items-center">
                <span 
                  :class="[
                    'px-3 py-1 rounded-full text-xs font-semibold',
                    userProfile?.email_status === 'vérifier' 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-yellow-100 text-yellow-800'
                  ]"
                >
                  {{ userProfile?.email_status === 'vérifier' ? '✓ Vérifié' : '⏳ En attente' }}
                </span>
              </div>

              <div class="mt-4 space-y-2">
                <div class="flex items-center justify-between text-sm">
                  <span class="text-gray-500">Jetons</span>
                  <span class="font-semibold text-primary-600">{{ userProfile?.jettons || 0 }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Middle & Right Columns - Forms -->
        <div class="lg:col-span-2">
          <div class="space-y-6">
            <!-- Personal Information Form -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Informations personnelles</h3>
              </div>
              <form @submit.prevent="updateProfile" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">
                      Nom
                    </label>
                    <input 
                      type="text" 
                      id="nom"
                      v-model="profileForm.nom"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                      placeholder="Votre nom"
                    />
                  </div>

                  <div>
                    <label for="prenom" class="block text-sm font-medium text-gray-700 mb-2">
                      Prénom
                    </label>
                    <input 
                      type="text" 
                      id="prenom"
                      v-model="profileForm.prenom"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                      placeholder="Votre prénom"
                    />
                  </div>

                  <div class="md:col-span-2">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                      Email <span class="text-gray-400 text-xs">(Non modifiable)</span>
                    </label>
                    <input 
                      type="email" 
                      id="email"
                      :value="userProfile?.email"
                      disabled
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500 cursor-not-allowed"
                    />
                  </div>

                  <div class="md:col-span-2">
                    <label for="adresse" class="block text-sm font-medium text-gray-700 mb-2">
                      Adresse
                    </label>
                    <input 
                      type="text" 
                      id="adresse"
                      v-model="profileForm.adresse"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                      placeholder="Votre adresse complète"
                    />
                  </div>

                  <div>
                    <label for="telephone" class="block text-sm font-medium text-gray-700 mb-2">
                      Téléphone
                    </label>
                    <input 
                      type="tel" 
                      id="telephone"
                      v-model="profileForm.telephone"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                      placeholder="+229 XX XX XX XX"
                    />
                  </div>

                  <div>
                    <label for="login" class="block text-sm font-medium text-gray-700 mb-2">
                      Login <span class="text-gray-400 text-xs">(Non modifiable)</span>
                    </label>
                    <input 
                      type="text" 
                      id="login"
                      :value="userProfile?.login"
                      disabled
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500 cursor-not-allowed"
                    />
                  </div>
                </div>

                <div class="mt-6 flex justify-end">
                  <button
                    type="submit"
                    :disabled="loadingProfile"
                    class="inline-flex justify-center items-center px-6 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50"
                  >
                    <svg v-if="!loadingProfile" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <svg v-else class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ loadingProfile ? 'Enregistrement...' : 'Enregistrer les modifications' }}
                  </button>
                </div>
              </form>
            </div>

            <!-- Change Password Form -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Modifier le mot de passe</h3>
                <p class="mt-1 text-sm text-gray-500">Assurez-vous d'utiliser un mot de passe sécurisé</p>
              </div>
              <form @submit.prevent="changePassword" class="p-6">
                <div class="space-y-4">
                  <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                      Mot de passe actuel
                    </label>
                    <div class="relative">
                      <input 
                        :type="showCurrentPassword ? 'text' : 'password'"
                        id="current_password"
                        v-model="passwordForm.currentPassword"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        placeholder="••••••••"
                        required
                      />
                      <button
                        type="button"
                        @click="showCurrentPassword = !showCurrentPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center"
                      >
                        <svg v-if="!showCurrentPassword" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg v-else class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                      </button>
                    </div>
                  </div>

                  <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                      Nouveau mot de passe <span class="text-xs text-gray-500">(minimum 8 caractères)</span>
                    </label>
                    <div class="relative">
                      <input 
                        :type="showNewPassword ? 'text' : 'password'"
                        id="new_password"
                        v-model="passwordForm.newPassword"
                        @input="validatePassword"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        placeholder="••••••••"
                        required
                        minlength="8"
                      />
                      <button
                        type="button"
                        @click="showNewPassword = !showNewPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center"
                      >
                        <svg v-if="!showNewPassword" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg v-else class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                      </button>
                    </div>
                    
                    <!-- Password Strength Indicator -->
                    <div v-if="passwordForm.newPassword" class="mt-2">
                      <div class="flex items-center space-x-2">
                        <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                          <div 
                            :class="[
                              'h-full transition-all duration-300',
                              passwordStrength.score === 1 ? 'bg-red-500 w-1/4' :
                              passwordStrength.score === 2 ? 'bg-yellow-500 w-2/4' :
                              passwordStrength.score === 3 ? 'bg-blue-500 w-3/4' :
                              passwordStrength.score === 4 ? 'bg-green-500 w-full' :
                              'bg-gray-300 w-0'
                            ]"
                          ></div>
                        </div>
                        <span 
                          :class="[
                            'text-xs font-medium',
                            passwordStrength.score === 1 ? 'text-red-600' :
                            passwordStrength.score === 2 ? 'text-yellow-600' :
                            passwordStrength.score === 3 ? 'text-blue-600' :
                            passwordStrength.score === 4 ? 'text-green-600' :
                            'text-gray-500'
                          ]"
                        >
                          {{ passwordStrength.label }}
                        </span>
                      </div>
                      <ul class="mt-2 space-y-1 text-xs">
                        <li :class="passwordStrength.hasMinLength ? 'text-green-600' : 'text-gray-500'">
                          <span v-if="passwordStrength.hasMinLength">✓</span>
                          <span v-else>○</span>
                          Au moins 8 caractères
                        </li>
                        <li :class="passwordStrength.hasUpperCase ? 'text-green-600' : 'text-gray-500'">
                          <span v-if="passwordStrength.hasUpperCase">✓</span>
                          <span v-else>○</span>
                          Une majuscule
                        </li>
                        <li :class="passwordStrength.hasLowerCase ? 'text-green-600' : 'text-gray-500'">
                          <span v-if="passwordStrength.hasLowerCase">✓</span>
                          <span v-else>○</span>
                          Une minuscule
                        </li>
                        <li :class="passwordStrength.hasNumber ? 'text-green-600' : 'text-gray-500'">
                          <span v-if="passwordStrength.hasNumber">✓</span>
                          <span v-else>○</span>
                          Un chiffre
                        </li>
                        <li :class="passwordStrength.hasSpecialChar ? 'text-green-600' : 'text-gray-500'">
                          <span v-if="passwordStrength.hasSpecialChar">✓</span>
                          <span v-else>○</span>
                          Un caractère spécial
                        </li>
                      </ul>
                    </div>
                  </div>

                  <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                      Confirmer le nouveau mot de passe
                    </label>
                    <input 
                      :type="showConfirmPassword ? 'text' : 'password'"
                      id="confirm_password"
                      v-model="passwordForm.confirmPassword"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                      :class="{ 'border-red-500': passwordForm.confirmPassword && passwordForm.newPassword !== passwordForm.confirmPassword }"
                      placeholder="••••••••"
                      required
                    />
                    <p v-if="passwordForm.confirmPassword && passwordForm.newPassword !== passwordForm.confirmPassword" class="mt-1 text-sm text-red-600">
                      Les mots de passe ne correspondent pas
                    </p>
                  </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                  <button
                    type="button"
                    @click="resetPasswordForm"
                    class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                  >
                    Annuler
                  </button>
                  <button
                    type="submit"
                    :disabled="loadingPassword || !isPasswordValid"
                    class="inline-flex justify-center items-center px-6 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <svg v-if="!loadingPassword" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    {{ loadingPassword ? 'Modification...' : 'Modifier le mot de passe' }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import axios from '@/api/axios'
import { authAPI } from '@/api/modules/auth'
import DashboardHeader from '@/components/layout/DashboardHeader.vue'
const authStore = useAuthStore()
const router = useRouter()

// State
const userProfile = ref(null)
const loadingProfile = ref(false)
const loadingPassword = ref(false)
const uploadingImage = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

// Profile Form
const profileForm = ref({
  nom: '',
  prenom: '',
  adresse: '',
  telephone: ''
})

// Password Form
const passwordForm = ref({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
})

const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)

// Image Upload
const selectedFile = ref(null)
const previewImage = ref(null)
const fileInput = ref(null)

// Computed
const profileImage = computed(() => {
  if (!userProfile.value?.image) return '/images/avatar.png'
  
  // Si l'image commence par http, c'est une URL Cloudinary
  if (userProfile.value.image.startsWith('http')) {
    return userProfile.value.image
  }
  
  // Sinon, c'est un chemin local
  return `/${userProfile.value.image}`
})

const passwordStrength = computed(() => {
  const password = passwordForm.value.newPassword
  if (!password) return { score: 0, label: '', hasMinLength: false, hasUpperCase: false, hasLowerCase: false, hasNumber: false, hasSpecialChar: false }

  const checks = {
    hasMinLength: password.length >= 8,
    hasUpperCase: /[A-Z]/.test(password),
    hasLowerCase: /[a-z]/.test(password),
    hasNumber: /\d/.test(password),
    hasSpecialChar: /[@$!%*?&]/.test(password)
  }

  const score = Object.values(checks).filter(Boolean).length

  let label = ''
  if (score === 1) label = 'Faible'
  else if (score === 2) label = 'Moyen'
  else if (score === 3 || score === 4) label = 'Bon'
  else if (score === 5) label = 'Excellent'

  return { score, label, ...checks }
})

const isPasswordValid = computed(() => {
  return passwordStrength.value.score === 5 && 
         passwordForm.value.newPassword === passwordForm.value.confirmPassword &&
         passwordForm.value.currentPassword.length > 0
})

// Methods
const fetchProfile = async () => {
  try {
    const response = await axios.get('/profile')
    // Backend retourne { success: true, data: {...} }
    const profileData = response.data.data || response.data
    userProfile.value = profileData
    
    // Populate form
    profileForm.value = {
      nom: profileData.nom || '',
      prenom: profileData.prenom || '',
      adresse: profileData.adresse || '',
      telephone: profileData.telephone || ''
    }
  } catch (error) {
    console.error('Erreur lors du chargement du profil:', error)
    errorMessage.value = 'Impossible de charger le profil'
  }
}

const updateProfile = async () => {
  loadingProfile.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    const response = await axios.put('/profile', profileForm.value)
    const profileData = response.data.data || response.data
    userProfile.value = profileData
    
    // Update auth store
    authStore.user = profileData
    
    successMessage.value = 'Profil mis à jour avec succès'
    
    setTimeout(() => {
      successMessage.value = ''
    }, 3000)
  } catch (error) {
    console.error('Erreur lors de la mise à jour:', error)
    errorMessage.value = error.response?.data?.message || 'Erreur lors de la mise à jour du profil'
  } finally {
    loadingProfile.value = false
  }
}

const handleFileSelect = (event) => {
  const file = event.target.files[0]
  if (!file) return

  // Validate file size (5MB max)
  if (file.size > 5 * 1024 * 1024) {
    errorMessage.value = 'L\'image ne doit pas dépasser 5MB'
    return
  }

  // Validate file type
  const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp']
  if (!validTypes.includes(file.type)) {
    errorMessage.value = 'Format d\'image non supporté. Utilisez JPG, PNG, GIF ou WEBP'
    return
  }

  selectedFile.value = file

  // Create preview
  const reader = new FileReader()
  reader.onload = (e) => {
    previewImage.value = e.target.result
  }
  reader.readAsDataURL(file)

  errorMessage.value = ''
}

const uploadProfileImage = async () => {
  if (!selectedFile.value) return

  uploadingImage.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    const formData = new FormData()
    formData.append('image', selectedFile.value)

    const response = await axios.post('/profile/image', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    // Backend retourne { success: true, data: { image_url: ... } }
    const imageUrl = response.data.data?.image_url || response.data.image_url
    if (userProfile.value) {
      userProfile.value.image = imageUrl
    }
    
    // Mettre à jour le store pour que les headers affichent la nouvelle photo
    if (authStore.user) {
      authStore.user.image = imageUrl
    }
    
    selectedFile.value = null
    previewImage.value = null

    successMessage.value = 'Photo de profil mise à jour avec succès'
    
    // Refresh profile
    await fetchProfile()

    setTimeout(() => {
      successMessage.value = ''
    }, 3000)
  } catch (error) {
    console.error('Erreur lors de l\'upload:', error)
    errorMessage.value = error.response?.data?.message || 'Erreur lors de l\'upload de l\'image'
    previewImage.value = null
    selectedFile.value = null
  } finally {
    uploadingImage.value = false
  }
}

const deleteProfileImage = async () => {
  if (!confirm('Êtes-vous sûr de vouloir supprimer votre photo de profil ?')) return

  try {
    await axios.delete('/profile/image')
    userProfile.value.image = 'public/avatar.png'
    
    // Mettre à jour le store pour que les headers affichent l'avatar par défaut
    if (authStore.user) {
      authStore.user.image = 'public/avatar.png'
    }
    
    previewImage.value = null
    successMessage.value = 'Photo de profil supprimée'
    
    setTimeout(() => {
      successMessage.value = ''
    }, 3000)
  } catch (error) {
    console.error('Erreur lors de la suppression:', error)
    errorMessage.value = 'Erreur lors de la suppression de la photo'
  }
}

const changePassword = async () => {
  if (!isPasswordValid.value) return

  loadingPassword.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    await authAPI.updatePassword({
      current_password: passwordForm.value.currentPassword,
      new_password: passwordForm.value.newPassword,
      new_password_confirmation: passwordForm.value.confirmPassword
    })
    
    successMessage.value = 'Mot de passe modifié avec succès. Vous allez être déconnecté...'
    resetPasswordForm()
    
    // Déconnecter l'utilisateur après 2 secondes
    setTimeout(async () => {
      await authStore.logout()
      router.push('/login')
    }, 2000)
    
  } catch (error) {
    console.error('Erreur lors du changement de mot de passe:', error)
    errorMessage.value = error.response?.data?.message || error.response?.data?.errors?.current_password?.[0] || 'Erreur lors du changement de mot de passe'
  } finally {
    loadingPassword.value = false
  }
}

const validatePassword = () => {
  // Just trigger the computed property to update
}

const resetPasswordForm = () => {
  passwordForm.value = {
    currentPassword: '',
    newPassword: '',
    confirmPassword: ''
  }
  showCurrentPassword.value = false
  showNewPassword.value = false
  showConfirmPassword.value = false
}

// Lifecycle
onMounted(() => {
  fetchProfile()
})
</script>

<style scoped>
/* Custom scrollbar for long content */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #555;
}

/* Smooth transitions */
* {
  transition: all 0.2s ease-in-out;
}

button:active {
  transform: scale(0.98);
}
</style>

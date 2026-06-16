<template>
  <div class="min-h-screen bg-gradient-to-br from-black via-gray-900 to-black flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Logo et Titre -->
      <div class="text-center">
        <router-link to="/" class="inline-block">
          <img src="/images/logo.png" alt="Olly Hans" class="h-16 mx-auto" />
        </router-link>
        <h2 class="mt-6 text-3xl font-bold text-white">
          Mot de passe oublié ?
        </h2>
        <p class="mt-2 text-sm text-gray-400">
          Pas de souci, nous vous aiderons à le réinitialiser.
        </p>
      </div>

      <!-- Formulaire -->
      <form @submit.prevent="handleSubmit" class="mt-8 space-y-6 bg-white rounded-lg shadow-xl p-8">
        <!-- Alerte succès -->
        <div v-if="successMessage" class="bg-success-50 border-l-4 border-success-500 p-4 rounded">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-success-500" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm text-success-700">{{ successMessage }}</p>
            </div>
          </div>
        </div>

        <!-- Alerte erreur -->
        <div v-if="errorMessage" class="bg-error-50 border-l-4 border-error-500 p-4 rounded">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-error-500" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm text-error-700">{{ errorMessage }}</p>
            </div>
          </div>
        </div>

        <div v-if="!emailSent">
          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
              Adresse email
            </label>
            <div class="mt-1 relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
              <input
                id="email"
                v-model="email"
                type="email"
                required
                autocomplete="email"
                class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                placeholder="email@example.com"
              />
            </div>
            <p class="mt-2 text-sm text-gray-500">
              Entrez l'adresse email associée à votre compte.
            </p>
          </div>

          <!-- Bouton Submit -->
          <div>
            <button
              type="submit"
              :disabled="loading"
              class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
            >
              <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ loading ? 'Envoi...' : 'Envoyer le lien de réinitialisation' }}
            </button>
          </div>
        </div>

        <div v-else class="text-center">
          <div class="w-16 h-16 bg-success-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-success-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Email envoyé !</h3>
          <p class="text-sm text-gray-600 mb-6">
            Un code OTP a été envoyé à votre adresse email. Utilisez-le pour réinitialiser votre mot de passe.
          </p>
          <router-link
            to="/reset-password"
            class="inline-flex items-center px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium"
          >
            Réinitialiser le mot de passe
            <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
          </router-link>
          <button
            type="button"
            @click="emailSent = false"
            class="block mt-4 mx-auto text-sm text-primary-600 hover:text-primary-500 font-medium"
          >
            Renvoyer l'email
          </button>
        </div>

        <!-- Séparateur -->
        <div class="relative">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-gray-500">Ou</span>
          </div>
        </div>

        <!-- Lien retour connexion -->
        <div>
          <router-link
            to="/login"
            class="w-full flex justify-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200"
          >
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour à la connexion
          </router-link>
        </div>
      </form>

      <!-- Retour accueil -->
      <div class="text-center">
        <router-link to="/" class="text-sm text-gray-400 hover:text-primary-400 transition-colors inline-flex items-center">
          <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Retour à l'accueil
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { authAPI } from '@/api/modules/auth'

const loading = ref(false)
const email = ref('')
const emailSent = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const handleSubmit = async () => {
  loading.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    await authAPI.forgotPassword(email.value)
    
    emailSent.value = true
    successMessage.value = 'Un code OTP a été envoyé à votre adresse email. Vérifiez votre boîte mail.'
  } catch (error) {
    console.error('Erreur forgot password:', error.response)
    if (error.response?.status === 404) {
      errorMessage.value = 'Aucun compte associé à cet email'
    } else if (error.response?.status === 422) {
      const errors = error.response?.data?.errors
      errorMessage.value = errors?.email?.[0] || error.response?.data?.message || 'Email invalide'
    } else {
      errorMessage.value = error.response?.data?.message || 'Une erreur est survenue lors de l\'envoi de l\'email'
    }
  } finally {
    loading.value = false
  }
}
</script>

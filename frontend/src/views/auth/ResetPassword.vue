<template>
  <div class="min-h-screen bg-gradient-to-br from-black via-gray-900 to-black flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Logo et Titre -->
      <div class="text-center">
        <router-link to="/" class="inline-block">
          <img src="/images/logo.png" alt="Olly Hans" class="h-16 mx-auto" />
        </router-link>
        <h2 class="mt-6 text-3xl font-bold text-white">
          Réinitialiser le mot de passe
        </h2>
        <p class="mt-2 text-sm text-gray-400">
          Entrez le code OTP reçu par email et votre nouveau mot de passe
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
              <p class="text-sm text-error-700 whitespace-pre-line">{{ errorMessage }}</p>
            </div>
          </div>
        </div>

        <!-- Email -->
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
            Email
          </label>
          <input 
            type="email" 
            id="email"
            v-model="formData.email"
            placeholder="votre@email.com"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
          />
        </div>

        <!-- Code OTP -->
        <div>
          <label for="otp" class="block text-sm font-medium text-gray-700 mb-2">
            Code OTP
          </label>
          <input 
            type="text" 
            id="otp"
            v-model="formData.otp"
            placeholder="123456"
            required
            maxlength="6"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
          />
          <p class="mt-1 text-xs text-gray-500">Entrez le code à 6 chiffres reçu par email</p>
        </div>

        <!-- Nouveau mot de passe -->
        <div>
          <label for="newPassword" class="block text-sm font-medium text-gray-700 mb-2">
            Nouveau mot de passe
          </label>
          <div class="relative">
            <input 
              :type="showPassword ? 'text' : 'password'" 
              id="newPassword"
              v-model="formData.newPassword"
              placeholder="••••••••"
              required
              minlength="8"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
            />
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
            >
              <svg v-if="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
              </svg>
            </button>
          </div>
          <p class="mt-1 text-xs text-gray-500">Minimum 8 caractères</p>
        </div>

        <!-- Confirmer mot de passe -->
        <div>
          <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-2">
            Confirmer le mot de passe
          </label>
          <input 
            :type="showPassword ? 'text' : 'password'" 
            id="confirmPassword"
            v-model="formData.confirmPassword"
            placeholder="••••••••"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
            :class="{ 'border-red-500': formData.confirmPassword && formData.newPassword !== formData.confirmPassword }"
          />
          <p v-if="formData.confirmPassword && formData.newPassword !== formData.confirmPassword" class="mt-1 text-xs text-red-600">
            Les mots de passe ne correspondent pas
          </p>
        </div>

        <!-- Bouton Soumettre -->
        <div>
          <button
            type="submit"
            :disabled="loading || !isFormValid"
            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
          >
            <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ loading ? 'Réinitialisation en cours...' : 'Réinitialiser le mot de passe' }}
          </button>
        </div>

        <!-- Retour connexion -->
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
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { authAPI } from '@/api/modules/auth'

const router = useRouter()

const loading = ref(false)
const showPassword = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const formData = ref({
  email: '',
  otp: '',
  newPassword: '',
  confirmPassword: ''
})

const isFormValid = computed(() => {
  return formData.value.email &&
         formData.value.otp &&
         formData.value.newPassword &&
         formData.value.confirmPassword &&
         formData.value.newPassword === formData.value.confirmPassword &&
         formData.value.newPassword.length >= 8
})

const handleSubmit = async () => {
  if (!isFormValid.value) return

  loading.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    await authAPI.resetPassword({
      email: formData.value.email,
      otp: formData.value.otp,
      password: formData.value.newPassword,
      password_confirmation: formData.value.confirmPassword
    })
    
    successMessage.value = 'Mot de passe réinitialisé avec succès ! Redirection vers la connexion...'
    
    setTimeout(() => {
      router.push('/login')
    }, 2000)
  } catch (error) {
    console.error('Erreur reset password:', error.response?.data)
    
    // Gestion des erreurs de validation (422)
    if (error.response?.status === 422) {
      const errors = error.response?.data?.errors
      if (errors) {
        // Construire un message d'erreur complet et clair
        const errorMessages = []
        
        // Erreurs sur l'email
        if (errors.email) {
          errorMessages.push(`📧 Email : ${errors.email[0]}`)
        }
        
        // Erreurs sur le code OTP
        if (errors.otp) {
          errorMessages.push(`🔢 Code OTP : ${errors.otp[0]}`)
        }
        
        // Erreurs sur le mot de passe
        if (errors.password) {
          errorMessages.push(`🔒 Mot de passe : ${errors.password[0]}`)
        }
        
        // Afficher toutes les erreurs sur des lignes séparées
        errorMessage.value = errorMessages.join('\n')
      } else {
        errorMessage.value = error.response?.data?.message || 'Veuillez vérifier tous les champs du formulaire.'
      }
    } 
    // Code OTP invalide ou expiré (400)
    else if (error.response?.status === 400) {
      const message = error.response?.data?.message
      if (message && message.includes('incorrect')) {
        errorMessage.value = '❌ Le code OTP que vous avez entré est incorrect. Veuillez vérifier et réessayer.'
      } else {
        errorMessage.value = '⏰ Code OTP expiré. Veuillez retourner à la page "Mot de passe oublié" pour demander un nouveau code.'
      }
    } 
    // Email non trouvé (404)
    else if (error.response?.status === 404) {
      errorMessage.value = '❌ Aucun compte n\'est associé à cette adresse email.'
    } 
    // Erreur serveur (500)
    else if (error.response?.status === 500) {
      errorMessage.value = '⚠️ Une erreur est survenue sur le serveur. Veuillez réessayer dans quelques instants.'
    }
    // Autres erreurs
    else {
      errorMessage.value = error.response?.data?.message || '⚠️ Une erreur inattendue est survenue. Veuillez vérifier votre connexion et réessayer.'
    }
  } finally {
    loading.value = false
  }
}
</script>

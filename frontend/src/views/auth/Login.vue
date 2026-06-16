<template>
  <div class="min-h-screen bg-gradient-to-br from-black via-gray-900 to-black flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Logo et Titre -->
      <div class="text-center">
        <router-link to="/" class="inline-block">
          <img src="/images/logo.png" alt="Olly Hans" class="h-16 mx-auto" />
        </router-link>
        <h2 class="mt-6 text-3xl font-bold text-white">
          Bon retour !
        </h2>
        <p class="mt-2 text-sm text-gray-400">
          Connectez-vous à votre compte Olly Hans
        </p>
      </div>

      <!-- Formulaire -->
      <form @submit.prevent="handleSubmit" class="mt-8 space-y-6 bg-white rounded-lg shadow-xl p-8">
        <!-- Alerte d'erreur -->
        <div v-if="errorMessage" class="bg-error-50 border-l-4 border-error-500 p-4 rounded">
          <div class="flex">
            <div class="flex-shrink-0">
              <!-- <svg class="h-5 w-5 text-error-500" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg> -->
            </div>
            <div class="ml-3">
              <p class="text-sm text-error-700">{{ errorMessage }}</p>
            </div>
          </div>
        </div>

        <!-- Succès -->
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

        <!-- Email ou Login -->
        <div>
          <label for="identifier" class="block text-sm font-medium text-gray-700">
            Email ou nom d'utilisateur
          </label>
          <div class="mt-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <input
              id="identifier"
              v-model="form.identifier"
              type="text"
              required
              autocomplete="username"
              class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
              placeholder="email@example.com ou username"
            />
          </div>
        </div>

        <!-- Mot de passe -->
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">
            Mot de passe
          </label>
          <div class="mt-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
            </div>
            <input
              id="password"
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              required
              autocomplete="current-password"
              class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
              placeholder="••••••••"
            />
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute inset-y-0 right-0 flex items-center pr-3"
            >
              <svg v-if="!showPassword" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              <svg v-else class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Se souvenir / Mot de passe oublié -->
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input
              id="remember-me"
              v-model="form.remember"
              type="checkbox"
              class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
            />
            <label for="remember-me" class="ml-2 block text-sm text-gray-700">
              Se souvenir de moi
            </label>
          </div>

          <div class="text-sm">
            <router-link to="/forgot-password" class="font-medium text-primary-600 hover:text-primary-500 transition-colors">
              Mot de passe oublié ?
            </router-link>
          </div>
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
            {{ loading ? 'Connexion...' : 'Se connecter' }}
          </button>
        </div>

        <!-- Séparateur -->
        <div class="relative">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-gray-500">Nouveau sur Olly Hans Distribution ?</span>
          </div>
        </div>

        <!-- Lien inscription -->
        <div>
          <router-link
            to="/register"
            class="w-full flex justify-center py-3 px-4 border border-primary-600 rounded-md shadow-sm text-sm font-medium text-primary-600 bg-white hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200"
          >
            Créer un compte
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
import { ref, reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const loading = ref(false)
const showPassword = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const form = reactive({
  identifier: '',
  password: '',
  remember: false,
})

const handleSubmit = async () => {
  loading.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    const result = await authStore.login(form)
    
    // Connexion réussie
    successMessage.value = 'Connexion réussie ! Redirection...'
    
    setTimeout(() => {
      if (authStore.userType === 'admin') {
        router.push({ name: 'admin-dashboard' })
      } else {
        const redirect = route.query.redirect || '/'
        router.push(redirect)
      }
    }, 1000)
  } catch (error) {
    console.error('Erreur de connexion:', error.response?.data)
    
    // Identifiants incorrects (401)
    if (error.response?.status === 401) {
      const errors = error.response?.data?.errors
      if (errors?.identifier) {
        errorMessage.value = `${errors.identifier[0]}`
      } else {
        errorMessage.value = 'Email/Login ou mot de passe incorrect. Veuillez réessayer.'
      }
    } 
    // Erreurs de validation (422)
    else if (error.response?.status === 422) {
      const errors = error.response?.data?.errors
      if (errors) {
        const errorMessages = []
        if (errors.identifier) errorMessages.push(errors.identifier[0])
        if (errors.password) errorMessages.push(errors.password[0])
        errorMessage.value = errorMessages.join(' ')
      } else {
        errorMessage.value = '⚠️ Veuillez vérifier vos informations de connexion.'
      }
    } 
    // Erreur réseau
    else if (error.code === 'ERR_NETWORK') {
      errorMessage.value = '🔌 Impossible de se connecter au serveur. Vérifiez que le backend est démarré.'
    } 
    // Erreur serveur (500)
    else if (error.response?.status === 500) {
      errorMessage.value = '⚠️ Une erreur est survenue sur le serveur. Veuillez réessayer dans quelques instants.'
    }
    // Autres erreurs
    else {
      errorMessage.value = error.response?.data?.message || '⚠️ Une erreur inattendue est survenue. Veuillez réessayer.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* Animations personnalisées si nécessaire */
</style>

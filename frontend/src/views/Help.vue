<template>
  <AppLayout>
    <div class="bg-gray-50 min-h-[calc(100vh-8rem)]">
      <!-- Hero Header -->
      <div class="bg-gradient-to-br from-primary-700 via-primary-600 to-primary-500 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
          <div class="flex flex-col items-center text-center">
            <!-- Icône IA animée -->
            <div class="relative mb-4">
              <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-lg">
                <svg class="w-9 h-9 sm:w-11 sm:h-11 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1 1 .3 2.7-1.119 2.7H4.917c-1.42 0-2.12-1.7-1.12-2.7L5 14.5" />
                </svg>
              </div>
              <!-- Indicateur online -->
              <span class="absolute -bottom-1 -right-1 flex h-4 w-4">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-4 w-4 bg-green-400 border-2 border-white"></span>
              </span>
            </div>

            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-2">Assistant IA Olly Hans Distribution</h1>
            <p class="text-primary-100 text-sm sm:text-base max-w-xl">
              Bonjour ! Je suis votre assistant virtuel. Posez-moi toutes vos questions sur la plateforme,
              je suis là pour vous aider 24h/24.
            </p>

            <!-- Suggestions rapides -->
            <div class="flex flex-wrap justify-center gap-2 mt-5">
              <button
                v-for="suggestion in quickSuggestions"
                :key="suggestion"
                @click="useSuggestion(suggestion)"
                :disabled="isLoading"
                class="text-xs sm:text-sm bg-white/20 hover:bg-white/30 disabled:opacity-50 disabled:cursor-not-allowed
                       text-white border border-white/30 rounded-full px-3 py-1.5 transition-all duration-200"
              >
                {{ suggestion }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Chat Interface -->
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 flex flex-col overflow-hidden"
             style="height: min(65vh, 640px);">

          <!-- Messages area -->
          <div ref="messagesContainer"
               class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4 scroll-smooth">

            <!-- Message de bienvenue -->
            <div v-if="messages.length === 0" class="flex flex-col items-center justify-center h-full text-center text-gray-400 py-8">
              <svg class="w-12 h-12 mb-3 text-primary-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
              </svg>
              <p class="text-sm sm:text-base font-medium text-gray-500">Commencez la conversation</p>
              <p class="text-xs sm:text-sm text-gray-400 mt-1">Posez votre question ci-dessous</p>
            </div>

            <!-- Messages -->
            <template v-for="(message, index) in messages" :key="index">

              <!-- Message utilisateur -->
              <div v-if="message.role === 'user'" class="flex justify-end">
                <div class="max-w-[85%] sm:max-w-[75%]">
                  <div class="bg-primary-600 text-white rounded-2xl rounded-tr-sm px-4 py-3 text-sm sm:text-base shadow-sm">
                    {{ message.content }}
                  </div>
                  <p class="text-xs text-gray-400 text-right mt-1">{{ message.time }}</p>
                </div>
              </div>

              <!-- Message IA -->
              <div v-else class="flex justify-start gap-3">
                <!-- Avatar IA -->
                <div class="flex-shrink-0 w-8 h-8 bg-primary-100 rounded-xl flex items-center justify-center mt-1">
                  <svg class="w-4 h-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1 1 .3 2.7-1.119 2.7H4.917c-1.42 0-2.12-1.7-1.12-2.7L5 14.5" />
                  </svg>
                </div>

                <div class="max-w-[85%] sm:max-w-[75%]">
                  <!-- Bulle de message -->
                  <div
                    :class="[
                      'rounded-2xl rounded-tl-sm px-4 py-3 text-sm sm:text-base shadow-sm',
                      message.isError
                        ? 'bg-red-50 border border-red-200 text-red-700'
                        : 'bg-gray-100 text-gray-800'
                    ]"
                  >
                    <!-- Contenu markdown simplifié -->
                    <div class="prose prose-sm max-w-none" v-html="renderMarkdown(message.content)"></div>

                    <!-- Curseur clignotant pendant le streaming -->
                    <span v-if="message.isStreaming"
                          class="inline-block w-0.5 h-4 bg-primary-500 ml-0.5 animate-pulse align-middle"></span>
                  </div>

                  <!-- Badge cache -->
                  <div class="flex items-center gap-2 mt-1">
                    <p class="text-xs text-gray-400">{{ message.time }}</p>
                    <span v-if="message.fromCache"
                          class="text-xs text-green-600 bg-green-50 border border-green-200 rounded-full px-2 py-0.5">
                      ⚡ Réponse rapide
                    </span>
                  </div>
                </div>
              </div>
            </template>

            <!-- Indicateur de chargement initial -->
            <div v-if="isLoading && !isStreaming" class="flex justify-start gap-3">
              <div class="flex-shrink-0 w-8 h-8 bg-primary-100 rounded-xl flex items-center justify-center">
                <svg class="w-4 h-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5" />
                </svg>
              </div>
              <div class="bg-gray-100 rounded-2xl rounded-tl-sm px-4 py-3">
                <div class="flex gap-1.5 items-center">
                  <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                  <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                  <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                </div>
              </div>
            </div>
          </div>

          <!-- Séparateur -->
          <div class="border-t border-gray-100"></div>

          <!-- Zone de saisie -->
          <div class="p-3 sm:p-4">
            <!-- Alerte erreur rate limit -->
            <div v-if="rateLimitError"
                 class="mb-3 flex items-center gap-2 bg-orange-50 border border-orange-200 rounded-xl px-3 py-2.5 text-sm text-orange-700">
              <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>{{ rateLimitError }}</span>
              <button @click="rateLimitError = null" class="ml-auto text-orange-500 hover:text-orange-700">✕</button>
            </div>

            <form @submit.prevent="sendMessage" class="flex gap-2 sm:gap-3">
              <div class="flex-1 relative">
                <textarea
                  ref="inputRef"
                  v-model="inputMessage"
                  @keydown.enter.exact.prevent="sendMessage"
                  @keydown.enter.shift.exact="() => {}"
                  @input="autoResize"
                  :disabled="isLoading"
                  placeholder="Posez votre question ici… (Entrée pour envoyer)"
                  rows="1"
                  class="w-full resize-none rounded-xl border border-gray-200 bg-gray-50
                         px-4 py-3 pr-12 text-sm sm:text-base text-gray-800 placeholder-gray-400
                         focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-transparent
                         disabled:opacity-60 disabled:cursor-not-allowed transition-all duration-200
                         max-h-32 overflow-y-auto"
                  style="min-height: 48px;"
                ></textarea>
                <!-- Compteur caractères -->
                <span class="absolute right-3 bottom-3 text-xs text-gray-300">
                  {{ inputMessage.length }}/1000
                </span>
              </div>

              <!-- Bouton annuler stream -->
              <button
                v-if="isStreaming"
                type="button"
                @click="cancelStream"
                class="flex-shrink-0 w-12 h-12 bg-gray-200 hover:bg-gray-300 text-gray-600
                       rounded-xl flex items-center justify-center transition-all duration-200"
                title="Arrêter la génération"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <rect x="6" y="6" width="12" height="12" rx="2" />
                </svg>
              </button>

              <!-- Bouton envoyer -->
              <button
                v-else
                type="submit"
                :disabled="isLoading || !inputMessage.trim() || inputMessage.length < 3"
                class="flex-shrink-0 w-12 h-12 bg-primary-600 hover:bg-primary-700 disabled:bg-primary-300
                       disabled:cursor-not-allowed text-white rounded-xl flex items-center justify-center
                       transition-all duration-200 shadow-sm hover:shadow-md"
                title="Envoyer (Entrée)"
              >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
              </button>
            </form>

            <!-- Footer info -->
            <div class="flex items-center justify-between mt-2 px-1">
              <p class="text-xs text-gray-400">
                <span class="text-primary-400 font-medium">Shift+Entrée</span> pour un saut de ligne
              </p>
              <p class="text-xs text-gray-400">
                Limité à
                <span class="font-medium">10 req/min</span>
              </p>
            </div>
          </div>
        </div>

        <!-- Bouton nouvelle conversation -->
        <div class="flex justify-center mt-4">
          <button
            @click="clearConversation"
            :disabled="messages.length === 0 && !isLoading"
            class="flex items-center gap-2 text-sm text-gray-500 hover:text-primary-600
                   disabled:opacity-40 disabled:cursor-not-allowed transition-colors duration-200"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Nouvelle conversation
          </button>
        </div>

        <!-- Informations complémentaires -->
        <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div
            v-for="info in helpCards"
            :key="info.title"
            class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 hover:shadow-md transition-shadow duration-200"
          >
            <div class="flex items-start gap-3">
              <div :class="`w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 ${info.bgClass}`">
                <span class="text-lg">{{ info.icon }}</span>
              </div>
              <div>
                <h3 class="text-sm font-semibold text-gray-800 mb-0.5">{{ info.title }}</h3>
                <p class="text-xs text-gray-500 leading-relaxed">{{ info.description }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, nextTick, onMounted, onUnmounted } from 'vue'
import DOMPurify from 'dompurify'
import AppLayout from '@/components/layout/AppLayout.vue'
import { aiAPI } from '@/api/modules/ai'

// ─── State ────────────────────────────────────────────────────────────────────
const messages          = ref([])
const inputMessage      = ref('')
const isLoading         = ref(false)
const isStreaming        = ref(false)
const rateLimitError    = ref(null)
const messagesContainer = ref(null)
const inputRef          = ref(null)

let abortController  = null
let sessionId        = generateSessionId()

// ─── Suggestions rapides ──────────────────────────────────────────────────────
const quickSuggestions = [
  'Comment publier un produit ?',
  'Comment fonctionne la plateforme ?',
  'Comment créer un compte ?',
  'Comment contacter le support ?',
]

// ─── Cartes d'information ────────────────────────────────────────────────────
const helpCards = [
  {
    icon: '💡',
    bgClass: 'bg-yellow-50',
    title: 'Conseils & Astuces',
    description: 'Demandez comment optimiser vos annonces et maximiser votre visibilité sur la plateforme.',
  },
  {
    icon: '🛡️',
    bgClass: 'bg-blue-50',
    title: 'Support 24h/24',
    description: 'Des réponses instantanées à toutes vos questions sur la plateforme.',
  },
  {
    icon: '📦',
    bgClass: 'bg-green-50',
    title: 'Produits & Services',
    description: 'Informations sur la publication de produits et l\'utilisation de la plateforme.',
  },
]

// ─── Helpers ─────────────────────────────────────────────────────────────────
function generateSessionId() {
  return 'sess_' + Math.random().toString(36).substring(2, 15) + Date.now().toString(36)
}

function getCurrentTime() {
  return new Intl.DateTimeFormat('fr-FR', { hour: '2-digit', minute: '2-digit' }).format(new Date())
}

/**
 * Rendu markdown minimal (gras, italique, code, listes, liens internes).
 */
function renderMarkdown(text) {
  if (!text) return ''
  let html = text
    // Échapper le HTML
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    // Gras **texte**
    .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
    // Italique *texte*
    .replace(/\*(.+?)\*/g, '<em>$1</em>')
    // Code inline `code`
    .replace(/`([^`]+)`/g, '<code class="bg-gray-200 text-gray-800 px-1 rounded text-xs font-mono">$1</code>')
    // Liens [texte](url) — bloquer javascript: protocol pour éviter XSS
    .replace(/\[([^\]]+)\]\(([^)]+)\)/g, (_, text, url) => {
      const safeUrl = /^\s*javascript\s*:/i.test(url) ? '#' : url
      return `<a href="${safeUrl}" class="text-primary-600 hover:underline" rel="noopener noreferrer">${text}</a>`
    })
    // Titres ## Titre
    .replace(/^### (.+)$/gm, '<h3 class="font-semibold text-gray-900 mt-2 mb-1">$1</h3>')
    .replace(/^## (.+)$/gm, '<h2 class="font-bold text-gray-900 mt-3 mb-1">$1</h2>')
    // Items de liste - item
    .replace(/^[-•] (.+)$/gm, '<li class="ml-4 list-disc">$1</li>')
    // Items numérotés 1. item
    .replace(/^\d+\. (.+)$/gm, '<li class="ml-4 list-decimal">$1</li>')
    // Paragraphes (double newline)
    .replace(/\n\n/g, '</p><p class="mt-2">')
    // Sauts de ligne simples
    .replace(/\n/g, '<br/>')

  return DOMPurify.sanitize(`<p>${html}</p>`, { ALLOWED_TAGS: ['p', 'strong', 'em', 'code', 'a', 'h2', 'h3', 'li', 'br'], ALLOWED_ATTR: ['href', 'class', 'rel'] })
}

// ─── Auto-resize textarea ────────────────────────────────────────────────────
function autoResize(event) {
  const el = event.target
  el.style.height = 'auto'
  el.style.height = Math.min(el.scrollHeight, 128) + 'px'
}

// ─── Scroll au bas ────────────────────────────────────────────────────────────
async function scrollToBottom() {
  await nextTick()
  if (messagesContainer.value) {
    messagesContainer.value.scrollTo({
      top: messagesContainer.value.scrollHeight,
      behavior: 'smooth',
    })
  }
}

// ─── Utiliser une suggestion ──────────────────────────────────────────────────
function useSuggestion(text) {
  if (isLoading.value) return
  inputMessage.value = text
  sendMessage()
}

// ─── Effacer la conversation ──────────────────────────────────────────────────
function clearConversation() {
  if (isLoading.value) cancelStream()
  messages.value = []
  inputMessage.value = ''
  rateLimitError.value = null
  sessionId = generateSessionId()
}

// ─── Annuler le streaming ─────────────────────────────────────────────────────
function cancelStream() {
  if (abortController) {
    abortController.abort()
    abortController = null
  }
  // Marquer le dernier message IA comme terminé
  const lastMsg = messages.value[messages.value.length - 1]
  if (lastMsg && lastMsg.role === 'assistant') {
    lastMsg.isStreaming = false
    if (!lastMsg.content) {
      lastMsg.content = '*(Génération arrêtée)*'
    }
  }
  isLoading.value   = false
  isStreaming.value = false
}

// ─── Envoyer un message ───────────────────────────────────────────────────────
async function sendMessage() {
  const question = inputMessage.value.trim()
  if (!question || question.length < 3 || isLoading.value) return

  // Effacer l'erreur précédente
  rateLimitError.value = null

  // Ajouter le message utilisateur
  messages.value.push({
    role: 'user',
    content: question,
    time: getCurrentTime(),
  })

  // Réinitialiser l'input
  inputMessage.value = ''
  if (inputRef.value) {
    inputRef.value.style.height = 'auto'
  }

  isLoading.value = true
  await scrollToBottom()

  // Créer le message IA en attente
  const aiMessageIndex = messages.value.length
  messages.value.push({
    role: 'assistant',
    content: '',
    time: getCurrentTime(),
    isStreaming: true,
    isError: false,
    fromCache: false,
  })

  await scrollToBottom()

  // Lancer le streaming
  let firstChunk = true
  abortController = await aiAPI.streamChat(
    question,
    sessionId,
    // onChunk
    (chunk) => {
      if (firstChunk) {
        isStreaming.value = true
        firstChunk = false
      }
      messages.value[aiMessageIndex].content += chunk
      // Détecter si c'est du cache (propriété 'cached' dans le chunk)
      scrollToBottom()
    },
    // onDone
    () => {
      if (messages.value[aiMessageIndex]) {
        messages.value[aiMessageIndex].isStreaming = false
      }
      isLoading.value   = false
      isStreaming.value = false
      abortController   = null
      scrollToBottom()
    },
    // onError
    (errorMsg) => {
      if (errorMsg.includes('Trop de') || errorMsg.includes('429')) {
        rateLimitError.value = errorMsg
        // Supprimer le message IA vide
        messages.value.splice(aiMessageIndex, 1)
      } else {
        messages.value[aiMessageIndex].content   = errorMsg
        messages.value[aiMessageIndex].isError   = true
        messages.value[aiMessageIndex].isStreaming = false
      }
      isLoading.value   = false
      isStreaming.value = false
    }
  )
}

// ─── Focus auto sur l'input ───────────────────────────────────────────────────
onMounted(() => {
  if (inputRef.value) {
    inputRef.value.focus()
  }
})

onUnmounted(() => {
  if (abortController) {
    abortController.abort()
  }
})
</script>

<style scoped>
/* Scroll bar stylisée */
.overflow-y-auto::-webkit-scrollbar {
  width: 4px;
}
.overflow-y-auto::-webkit-scrollbar-track {
  background: transparent;
}
.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 2px;
}
.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #cbd5e1;
}

/* Prose styles dans les messages IA */
.prose :deep(strong) {
  font-weight: 600;
  color: #1f2937;
}
.prose :deep(em) {
  font-style: italic;
  color: #374151;
}
.prose :deep(a) {
  color: #2563eb;
  text-decoration: underline;
}
.prose :deep(li) {
  margin: 2px 0;
}
.prose :deep(h2),
.prose :deep(h3) {
  line-height: 1.4;
}
</style>

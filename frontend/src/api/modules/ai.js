import axios from '../axios'

/**
 * Module API pour l'assistant IA Groq
 * Utilise fetch() natif pour le streaming SSE plutôt qu'axios
 */

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api/v1'

export const aiAPI = {
  /**
   * Envoie une question à l'assistant IA en mode streaming (SSE).
   * @param {string} question - La question de l'utilisateur
   * @param {string} sessionId - Identifiant de session
   * @param {function} onChunk - Callback appelé pour chaque morceau reçu (string)
   * @param {function} onDone - Callback appelé quand le stream est terminé
   * @param {function} onError - Callback appelé en cas d'erreur
   * @returns {AbortController} - Pour annuler le stream si nécessaire
   */
  async streamChat(question, sessionId, onChunk, onDone, onError) {
    const controller = new AbortController()

    const token = localStorage.getItem('token')
    const headers = {
      'Content-Type': 'application/json',
      'Accept': 'text/event-stream',
      'X-Session-ID': sessionId,
    }
    if (token) {
      headers['Authorization'] = `Bearer ${token}`
    }

    try {
      const response = await fetch(`${API_BASE_URL}/ai/chat`, {
        method: 'POST',
        headers,
        body: JSON.stringify({ question, session_id: sessionId }),
        signal: controller.signal,
      })

      if (!response.ok) {
        const errData = await response.json().catch(() => ({}))
        if (response.status === 429) {
          onError(errData.error || 'Trop de requêtes. Veuillez patienter 1 minute.')
        } else if (response.status === 422) {
          onError('Question invalide. Veuillez entrer au moins 3 caractères.')
        } else {
          onError(errData.error || `Erreur ${response.status}. Veuillez réessayer.`)
        }
        onDone()
        return controller
      }

      const reader = response.body.getReader()
      const decoder = new TextDecoder('utf-8')
      let buffer = ''

      const processStream = async () => {
        while (true) {
          const { done, value } = await reader.read()
          if (done) {
            onDone()
            break
          }

          buffer += decoder.decode(value, { stream: true })
          const lines = buffer.split('\n')
          buffer = lines.pop() // garder le dernier fragment incomplet

          for (const line of lines) {
            const trimmed = line.trim()
            if (!trimmed || !trimmed.startsWith('data: ')) continue

            const data = trimmed.slice(6)
            if (data === '[DONE]') {
              onDone()
              return
            }

            try {
              const parsed = JSON.parse(data)
              if (parsed.error) {
                onError(parsed.error)
                onDone()
                return
              }
              if (parsed.content !== undefined) {
                onChunk(parsed.content)
              }
            } catch {
              // Ignorer les chunks non-JSON
            }
          }
        }
      }

      processStream().catch((err) => {
        if (err.name !== 'AbortError') {
          onError('La connexion a été interrompue. Veuillez réessayer.')
          onDone()
        }
      })
    } catch (err) {
      if (err.name !== 'AbortError') {
        onError('Impossible de contacter le serveur. Vérifiez votre connexion.')
        onDone()
      }
    }

    return controller
  },

  /**
   * Vérifie l'état de l'API IA
   */
  health() {
    return axios.get('/ai/health')
  },
}

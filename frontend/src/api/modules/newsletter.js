import axios from '../axios'

export const newsletterAPI = {
  /**
   * S'abonner à la newsletter
   */
  subscribe(data) {
    return axios.post('/newsletter/subscribe', data)
  },

  /**
   * Se désabonner via le token
   */
  unsubscribe(token) {
    return axios.get(`/newsletter/unsubscribe/${token}`)
  },

  // ===== Routes Admin =====

  /**
   * Statistiques de la newsletter (admin)
   */
  getStats() {
    return axios.get('/admin/newsletter/stats')
  },

  /**
   * Liste paginée des abonnés (admin)
   */
  getSubscribers(params) {
    return axios.get('/admin/newsletter/subscribers', { params })
  }
}

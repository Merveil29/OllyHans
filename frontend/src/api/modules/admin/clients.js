import axios from '../../axios'

export const clientsAPI = {
  /**
   * Liste tous les clients
   */
  getAll() {
    return axios.get('/admin/clients')
  },

  /**
   * Détails d'un client
   */
  getById(id) {
    return axios.get(`/admin/clients/${id}`)
  },

  /**
   * Supprimer un client (et ses produits et sponsors)
   */
  delete(id) {
    return axios.delete(`/admin/clients/${id}`)
  },

  /**
   * Mettre à jour les jetons d'un client
   */
  updateJetons(id, data) {
    return axios.put(`/admin/clients/${id}/jetons`, data)
  }
}

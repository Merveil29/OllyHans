import axios from '../axios'

export const productsAPI = {
  // ===== Routes Publiques =====
  
  /**
   * Liste des produits validés (affichage public)
   */
  getPublicProducts(params) {
    return axios.get('/products', { params })
  },
  
  /**
   * Obtenir des suggestions de produits basées sur une recherche
   */
  getSuggestions(search) {
    return axios.get('/products/suggestions', { params: { search } })
  },
  
  /**
   * Détail d'un produit validé (affichage public)
   */
  getPublicProduct(id) {
    return axios.get(`/products/${id}`)
  },

  // ===== Routes Client (Authentifié) =====
  
  /**
   * Liste de mes produits (tous états)
   */
  getMyProducts() {
    return axios.get('/client/produits')
  },
  
  /**
   * Créer un nouveau produit
   * Déduit automatiquement 1 jeton client
   */
  createProduct(formData) {
    return axios.post('/client/produits', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
  },
  
  /**
   * Voir le détail de mon produit
   */
  getMyProduct(id) {
    return axios.get(`/client/produits/${id}`)
  },
  
  /**
   * Modifier mon produit (seulement si non validé)
   */
  updateProduct(id, formData) {
    return axios.put(`/client/produits/${id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
  },
  
  /**
   * Supprimer mon produit (peut supprimer même si validé)
   */
  deleteProduct(id) {
    return axios.delete(`/client/produits/${id}`)
  }
}

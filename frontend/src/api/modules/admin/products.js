import axios from '@/api/axios'

/**
 * API Admin - Gestion des Produits
 */

/**
 * Récupérer tous les produits
 * @param {Object} params - Paramètres de filtrage
 * @param {string} params.state - Filtrer par état (1=en attente, 2=validé, 3=rejeté)
 * @param {string} params.client - Filtrer par client ID
 * @param {string} params.categorie - Filtrer par catégorie ID
 * @param {string} params.sous_categorie - Filtrer par sous-catégorie ID
 * @param {number} params.per_page - Nombre d'éléments par page
 * @param {number} params.page - Numéro de page
 */
export const getProducts = (params = {}) => {
  return axios.get('/admin/products', { params })
}

/**
 * Récupérer le détail d'un produit
 * @param {number} id - ID du produit
 */
export const getProduct = (id) => {
  return axios.get(`/admin/products/${id}`)
}

/**
 * Récupérer les statistiques des produits
 */
export const getProductStats = () => {
  return axios.get('/admin/products/stats')
}

/**
 * Valider un produit
 * @param {number} id - ID du produit
 * @param {Object} data - Données optionnelles
 * @param {string} data.comment - Commentaire optionnel de l'admin
 */
export const validateProduct = (id, data = {}) => {
  return axios.put(`/admin/products/${id}/approve`, data)
}

/**
 * Rejeter un produit
 * @param {number} id - ID du produit
 * @param {Object} data - Données (reason obligatoire)
 * @param {string} data.reason - Raison obligatoire du rejet
 */
export const rejectProduct = (id, data) => {
  return axios.put(`/admin/products/${id}/reject`, data)
}

/**
 * Créer un produit (admin)
 * @param {FormData} formData - Données du formulaire avec images
 */
export const createProduct = (formData) => {
  return axios.post('/admin/products', formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  })
}

/**
 * Mettre à jour un produit (admin)
 * @param {number} id - ID du produit
 * @param {FormData} formData - Données du formulaire avec images
 */
export const updateProduct = (id, formData) => {
  return axios.post(`/admin/products/${id}`, formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  })
}

/**
 * Supprimer un produit
 * @param {number} id - ID du produit
 */
export const deleteProduct = (id) => {
  return axios.delete(`/admin/products/${id}`)
}

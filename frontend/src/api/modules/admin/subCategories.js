import axios from '@/api/axios'

/**
 * API Admin - Gestion des Sous-Catégories
 */

/**
 * Récupérer toutes les sous-catégories groupées par catégorie (route publique)
 */
export const getAllSubCategories = () => {
  return axios.get('/sub-categories/all')
}

/**
 * Récupérer toutes les sous-catégories d'une catégorie
 * @param {number} categoryId - ID de la catégorie
 */
export const getSubCategories = (categoryId) => {
  return axios.get(`/admin/categories/${categoryId}/sub-categories`)
}

/**
 * Récupérer une sous-catégorie avec ses produits
 * @param {number} id - ID de la sous-catégorie
 */
export const getSubCategory = (id) => {
  return axios.get(`/admin/sub-categories/${id}`)
}

/**
 * Créer une nouvelle sous-catégorie
 * @param {Object} data - Données de la sous-catégorie
 * @param {string} data.libelle_sous_categorie - Libellé
 * @param {number} data.id_categorie - ID de la catégorie parente
 */
export const createSubCategory = (data) => {
  return axios.post('/admin/sub-categories', data)
}

/**
 * Mettre à jour une sous-catégorie
 * @param {number} id - ID de la sous-catégorie
 * @param {Object} data - Données de la sous-catégorie
 * @param {string} data.libelle_sous_categorie - Libellé
 */
export const updateSubCategory = (id, data) => {
  return axios.put(`/admin/sub-categories/${id}`, data)
}

/**
 * Supprimer une sous-catégorie
 * @param {number} id - ID de la sous-catégorie
 */
export const deleteSubCategory = (id) => {
  return axios.delete(`/admin/sub-categories/${id}`)
}

import axios from '@/api/axios'

/**
 * API Admin - Gestion des Administrateurs
 */

/**
 * Récupérer tous les administrateurs
 */
export const getAdminUsers = () => {
  return axios.get('/admin/users')
}

/**
 * Récupérer les détails d'un administrateur
 * @param {number} id - ID de l'administrateur
 */
export const getAdminUser = (id) => {
  return axios.get(`/admin/users/${id}`)
}

/**
 * Créer un nouvel administrateur
 * @param {Object} data - Données de l'administrateur
 * @param {string} data.user_nom - Nom
 * @param {string} data.user_prenom - Prénom
 * @param {string} data.user_email - Email
 * @param {string} data.user_login - Login
 * @param {string} data.user_tel - Téléphone
 */
export const createAdminUser = (data) => {
  return axios.post('/admin/users', data)
}

/**
 * Supprimer un administrateur
 * @param {number} id - ID de l'administrateur
 */
export const deleteAdminUser = (id) => {
  return axios.delete(`/admin/users/${id}`)
}

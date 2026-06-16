import axios from '@/api/axios'

/**
 * API Admin - Gestion du Profil Administrateur
 */

/**
 * Récupérer le profil de l'administrateur connecté
 */
export const getAdminProfile = () => {
  return axios.get('/admin/profile')
}

/**
 * Mettre à jour le profil de l'administrateur
 * @param {Object} data - Données du profil
 * @param {string} data.user_nom - Nom
 * @param {string} data.user_prenom - Prénom
 * @param {string} data.user_email - Email
 * @param {string} data.user_login - Login
 * @param {string} data.user_tel - Téléphone
 */
export const updateAdminProfile = (data) => {
  return axios.put('/admin/profile', data)
}

/**
 * Mettre à jour l'image de profil de l'administrateur
 * @param {FormData} formData - FormData contenant l'image
 */
export const updateAdminImage = (formData) => {
  return axios.post('/admin/profile/image', formData, {
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  })
}

/**
 * Supprimer l'image de profil de l'administrateur
 */
export const deleteAdminImage = () => {
  return axios.delete('/admin/profile/image')
}

/**
 * Mettre à jour le mot de passe de l'administrateur
 * @param {Object} data - Données du mot de passe
 * @param {string} data.current_password - Mot de passe actuel
 * @param {string} data.new_password - Nouveau mot de passe
 * @param {string} data.new_password_confirmation - Confirmation du nouveau mot de passe
 */
export const updateAdminPassword = (data) => {
  return axios.put('/admin/profile/password', data)
}

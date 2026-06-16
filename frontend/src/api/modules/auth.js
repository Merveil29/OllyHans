import axios from '../axios'

export const authAPI = {
  register(data) {
    return axios.post('/register', data)
  },
  
  verifyOtp(data) {
    return axios.post('/verify-otp', data)
  },
  
  login(credentials) {
    return axios.post('/login', credentials)
  },
  
  logout() {
    return axios.post('/logout')
  },
  
  checkEmail(email) {
    return axios.get(`/check-email/${email}`)
  },
  
  checkLogin(login) {
    return axios.get(`/check-login/${login}`)
  },
  
  forgotPassword(email) {
    return axios.post('/forgot-password', { email })
  },
  
  resetPassword(data) {
    return axios.post('/reset-password', data)
  },
  
  activateAccount(code) {
    return axios.get(`/activate/${code}`)
  },
  
  getProfile() {
    return axios.get('/profile')
  },
  
  updateProfile(data) {
    return axios.put('/profile', data)
  },

  updateProfileImage(formData) {
    return axios.post('/profile/image', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
  },

  deleteProfileImage() {
    return axios.delete('/profile/image')
  },

  updatePassword(data) {
    return axios.put('/profile/password', data)
  }
}

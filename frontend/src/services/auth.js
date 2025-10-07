import api from './api'

export const authService = {
  async register(email, password, name) {
    const response = await api.post('/api/register', { email, password, name })
    return response.data
  },

  async login(email, password) {
    const response = await api.post('/api/login', { email, password })
    return response.data
  },

  loginWithGoogle() {
    // Redirect to backend OAuth endpoint
    window.location.href = `${import.meta.env.VITE_API_URL}/connect/google`
  },

  async getMe() {
    const response = await api.get('/api/me')
    return response.data
  },

  logout() {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
  },

  isAuthenticated() {
    return !!localStorage.getItem('token')
  },

  getToken() {
    return localStorage.getItem('token')
  },

  saveAuth(token, user) {
    localStorage.setItem('token', token)
    localStorage.setItem('user', JSON.stringify(user))
  },
}

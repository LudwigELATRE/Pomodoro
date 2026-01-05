import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authService } from '../services/auth'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(null)
  const loading = ref(false)
  const error = ref(null)

  const isAuthenticated = computed(() => !!token.value)

  // Initialize from localStorage
  function init() {
    try {
      const savedToken = localStorage.getItem('token')
      const savedUser = localStorage.getItem('user')

      if (savedToken && savedUser) {
        token.value = savedToken
        user.value = JSON.parse(savedUser)
      }
    } catch (e) {
      console.warn('Failed to load auth from localStorage, clearing...', e)
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      token.value = null
      user.value = null
    }
  }

  async function register(email, password, name) {
    loading.value = true
    error.value = null
    try {
      const data = await authService.register(email, password, name)
      token.value = data.token
      user.value = data.user
      authService.saveAuth(data.token, data.user)
      return data
    } catch (err) {
      error.value = err.response?.data?.error || 'Registration failed'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function login(email, password) {
    loading.value = true
    error.value = null
    try {
      const data = await authService.login(email, password)
      token.value = data.token
      user.value = data.user
      authService.saveAuth(data.token, data.user)
      return data
    } catch (err) {
      error.value = err.response?.data?.error || 'Login failed'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function loginWithGoogle(googleToken) {
    loading.value = true
    error.value = null
    try {
      const data = await authService.loginWithGoogle(googleToken)
      token.value = data.token
      user.value = data.user
      authService.saveAuth(data.token, data.user)
      return data
    } catch (err) {
      // Delay error display to avoid showing it during redirect
      setTimeout(() => {
        error.value = err.response?.data?.error || 'Google login failed'
      }, 2000)
      throw err
    } finally {
      loading.value = false
    }
  }

  async function fetchUser() {
    try {
      const userData = await authService.getMe()
      user.value = userData
      localStorage.setItem('user', JSON.stringify(userData))
    } catch (err) {
      console.error('Failed to fetch user:', err)
    }
  }

  function logout() {
    token.value = null
    user.value = null
    authService.logout()
  }

  return {
    user,
    token,
    loading,
    error,
    isAuthenticated,
    init,
    register,
    login,
    loginWithGoogle,
    fetchUser,
    logout,
  }
})

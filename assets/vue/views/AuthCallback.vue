<template>
  <div class="auth-callback">
    <div v-if="showError && error" class="error">
      {{ error }}
    </div>
    <div v-else class="loading">
      Connexion en cours...
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()
const error = ref(null)
const showError = ref(false)

async function attemptLogin(token, retries = 5, delay = 1500) {
  for (let i = 0; i < retries; i++) {
    try {
      // Wait before attempting to let API process Google user creation
      const waitTime = i === 0 ? 1500 : delay
      await new Promise(resolve => setTimeout(resolve, waitTime))

      // Save token
      localStorage.setItem('token', token)

      // Try to fetch user using api service (relative URL works for same domain)
      const response = await fetch('/api/me', {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      })

      if (!response.ok) {
        // Retry on error
        continue
      }

      const userData = await response.json()

      // Update store and save user data
      authStore.$patch({
        user: userData,
        token: token
      })
      localStorage.setItem('user', JSON.stringify(userData))

      // Success - redirect to home
      await router.push('/')
      return true
    } catch (err) {
      // Retry silently on error

      // If this was the last retry, show error after delay
      if (i === retries - 1) {
        error.value = 'Échec de l\'authentification. Veuillez réessayer.'
        localStorage.removeItem('token')
        // Show error message after 2 seconds to avoid confusion
        setTimeout(() => {
          showError.value = true
        }, 2000)
        setTimeout(() => router.push('/login'), 5000)
        return false
      }
    }
  }
}

onMounted(async () => {
  const urlParams = new URLSearchParams(window.location.search)
  const token = urlParams.get('token')

  if (token) {
    await attemptLogin(token)
  } else {
    error.value = 'Token manquant'
    // Show error message after 2 seconds
    setTimeout(() => {
      showError.value = true
    }, 2000)
    setTimeout(() => router.push('/login'), 4000)
  }
})
</script>

<style scoped>
.auth-callback {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
}

.loading {
  font-size: 1.2rem;
  color: #2196F3;
}

.error {
  font-size: 1.2rem;
  color: #f44336;
}
</style>

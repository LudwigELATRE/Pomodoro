<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-pomodoro-red to-red-700 px-4">
    <div class="absolute top-4 right-4">
      <LanguageSwitcher />
    </div>
    <div class="card max-w-md w-full">
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $t('auth.welcome') }}</h1>
        <p class="text-gray-600">{{ $t('auth.description') }}</p>
      </div>

      <!-- Tabs -->
      <div class="flex gap-2 mb-6">
        <button
          @click="activeTab = 'login'"
          :class="[
            'flex-1 py-2 px-4 rounded-lg font-semibold transition',
            activeTab === 'login'
              ? 'bg-pomodoro-red text-white'
              : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
          ]"
        >
          {{ $t('auth.login') }}
        </button>
        <button
          @click="activeTab = 'register'"
          :class="[
            'flex-1 py-2 px-4 rounded-lg font-semibold transition',
            activeTab === 'register'
              ? 'bg-pomodoro-red text-white'
              : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
          ]"
        >
          {{ $t('auth.register') }}
        </button>
      </div>

      <!-- Error Message -->
      <div v-if="authStore.error" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
        {{ authStore.error }}
      </div>

      <!-- Login Form -->
      <form v-if="activeTab === 'login'" @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('auth.email') }}</label>
          <input
            v-model="loginForm.email"
            type="email"
            required
            class="input"
            :placeholder="$t('auth.emailPlaceholder')"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('auth.password') }}</label>
          <input
            v-model="loginForm.password"
            type="password"
            required
            class="input"
            placeholder="********"
          />
        </div>
        <button
          type="submit"
          :disabled="authStore.loading"
          class="btn-primary w-full"
        >
          {{ authStore.loading ? $t('auth.loggingIn') : $t('auth.login') }}
        </button>
      </form>

      <!-- Register Form -->
      <form v-if="activeTab === 'register'" @submit.prevent="handleRegister" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('auth.name') }}</label>
          <input
            v-model="registerForm.name"
            type="text"
            required
            class="input"
            :placeholder="$t('auth.namePlaceholder')"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('auth.email') }}</label>
          <input
            v-model="registerForm.email"
            type="email"
            required
            class="input"
            :placeholder="$t('auth.emailPlaceholder')"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('auth.password') }}</label>
          <input
            v-model="registerForm.password"
            type="password"
            required
            class="input"
            placeholder="********"
          />
        </div>
        <button
          type="submit"
          :disabled="authStore.loading"
          class="btn-primary w-full"
        >
          {{ authStore.loading ? $t('auth.registering') : $t('auth.register') }}
        </button>
      </form>

      <!-- Divider -->
      <div class="relative my-6">
        <div class="absolute inset-0 flex items-center">
          <div class="w-full border-t border-gray-300"></div>
        </div>
        <div class="relative flex justify-center text-sm">
          <span class="px-2 bg-white text-gray-500">{{ $t('auth.orContinueWith') }}</span>
        </div>
      </div>

      <!-- Google Sign In -->
      <div id="google-signin-button" class="flex justify-center"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'
import LanguageSwitcher from '../components/LanguageSwitcher.vue'

const authStore = useAuthStore()
const router = useRouter()

const activeTab = ref('login')
const loginForm = ref({
  email: '',
  password: ''
})
const registerForm = ref({
  name: '',
  email: '',
  password: ''
})

async function handleLogin() {
  try {
    await authStore.login(loginForm.value.email, loginForm.value.password)
    router.push('/')
  } catch (err) {
    // Error is handled by the store
    console.error('Login failed:', err)
  }
}

async function handleRegister() {
  try {
    await authStore.register(
      registerForm.value.email,
      registerForm.value.password,
      registerForm.value.name
    )
    router.push('/')
  } catch (err) {
    // Error is handled by the store
    console.error('Registration failed:', err)
  }
}

async function handleGoogleCallback(response) {
  try {
    await authStore.loginWithGoogle(response.credential)
    router.push('/')
  } catch (err) {
    console.error('Google login failed:', err)
  }
}

onMounted(() => {
  // Initialize Google Sign-In
  if (window.google && window.google.accounts) {
    window.google.accounts.id.initialize({
      client_id: "285344658655-rnimqofkgbt2thdgqa0alvsmt5d0shi9.apps.googleusercontent.com" || 'YOUR_GOOGLE_CLIENT_ID',
      callback: handleGoogleCallback
    })

    window.google.accounts.id.renderButton(
      document.getElementById('google-signin-button'),
      {
        theme: 'outline',
        size: 'large',
        width: 350,
        text: 'continue_with'
      }
    )
  } else {
    console.warn('Google Identity Services not loaded')
  }
})
</script>

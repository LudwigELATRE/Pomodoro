<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from './stores/auth'

const authStore = useAuthStore()

onMounted(async () => {
  try {
    await authStore.init()
  } catch (error) {
    console.error('Failed to initialize auth store:', error)
    // Clear localStorage if init fails
    localStorage.clear()
    sessionStorage.clear()
  }
})
</script>

<template>
  <div class="min-h-screen">
    <router-view />
  </div>
</template>
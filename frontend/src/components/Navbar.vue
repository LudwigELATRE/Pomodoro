<template>
  <nav class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <div class="flex items-center gap-8">
          <h1 class="text-xl font-bold text-pomodoro-red">Pomodoro Timer</h1>
          <div class="hidden md:flex gap-4">
            <router-link
              to="/"
              class="px-3 py-2 rounded-md text-sm font-medium"
              :class="isActive('/') ? 'text-gray-900 bg-gray-100' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'"
            >
              {{ $t('nav.timer') }}
            </router-link>
            <router-link
              to="/statistics"
              class="px-3 py-2 rounded-md text-sm font-medium"
              :class="isActive('/statistics') ? 'text-gray-900 bg-gray-100' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'"
            >
              {{ $t('nav.statistics') }}
            </router-link>
            <router-link
              to="/settings"
              class="px-3 py-2 rounded-md text-sm font-medium"
              :class="isActive('/settings') ? 'text-gray-900 bg-gray-100' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'"
            >
              {{ $t('nav.settings') }}
            </router-link>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <LanguageSwitcher />
          <div class="flex items-center gap-2">
            <img
              v-if="authStore.user?.avatar"
              :src="authStore.user.avatar"
              :alt="authStore.user.name"
              class="w-8 h-8 rounded-full"
            />
            <div
              v-else
              class="w-8 h-8 rounded-full bg-pomodoro-red text-white flex items-center justify-center font-semibold"
            >
              {{ authStore.user?.name?.charAt(0).toUpperCase() || 'U' }}
            </div>
            <span class="text-sm font-medium text-gray-700">{{ authStore.user?.name }}</span>
          </div>
          <button @click="handleLogout" class="btn-secondary text-sm py-2">
            {{ $t('nav.logout') }}
          </button>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useAuth } from '../composables/useAuth'
import LanguageSwitcher from './LanguageSwitcher.vue'

const route = useRoute()
const authStore = useAuthStore()
const { logout: handleLogout } = useAuth()

const isActive = (path) => {
  return route.path === path
}
</script>
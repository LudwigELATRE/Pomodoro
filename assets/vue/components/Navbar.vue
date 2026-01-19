<template>
  <nav class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo -->
        <h1 class="text-xl font-bold text-pomodoro-red">Pomodoro Timer</h1>

        <!-- Desktop Navigation -->
        <div class="hidden md:flex items-center gap-4">
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

        <!-- Desktop Right Side -->
        <div class="hidden md:flex items-center gap-3">
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

        <!-- Mobile Burger Button -->
        <button
          @click="toggleMobileMenu"
          class="md:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100"
          aria-label="Menu"
        >
          <svg
            v-if="!isMobileMenuOpen"
            class="w-6 h-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
          <svg
            v-else
            class="w-6 h-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Mobile Menu -->
    <div
      v-if="isMobileMenuOpen"
      class="md:hidden border-t border-gray-200 bg-white"
    >
      <div class="px-4 py-3 space-y-1">
        <!-- User Section -->
        <div class="flex items-center gap-3 px-3 py-3 border-b border-gray-100 mb-2">
          <img
            v-if="authStore.user?.avatar"
            :src="authStore.user.avatar"
            :alt="authStore.user.name"
            class="w-10 h-10 rounded-full"
          />
          <div
            v-else
            class="w-10 h-10 rounded-full bg-pomodoro-red text-white flex items-center justify-center font-semibold text-lg"
          >
            {{ authStore.user?.name?.charAt(0).toUpperCase() || 'U' }}
          </div>
          <div>
            <p class="text-sm font-medium text-gray-900">{{ authStore.user?.name }}</p>
            <p class="text-xs text-gray-500">{{ authStore.user?.email }}</p>
          </div>
        </div>

        <!-- Timer Link -->
        <router-link
          to="/"
          @click="closeMobileMenu"
          class="flex items-center gap-3 px-3 py-3 rounded-md text-base font-medium"
          :class="isActive('/') ? 'text-pomodoro-red bg-red-50' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          {{ $t('nav.timer') }}
        </router-link>

        <!-- Statistics Link -->
        <router-link
          to="/statistics"
          @click="closeMobileMenu"
          class="flex items-center gap-3 px-3 py-3 rounded-md text-base font-medium"
          :class="isActive('/statistics') ? 'text-pomodoro-red bg-red-50' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg>
          {{ $t('nav.statistics') }}
        </router-link>

        <!-- Settings Link -->
        <router-link
          to="/settings"
          @click="closeMobileMenu"
          class="flex items-center gap-3 px-3 py-3 rounded-md text-base font-medium"
          :class="isActive('/settings') ? 'text-pomodoro-red bg-red-50' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100'"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          {{ $t('nav.settings') }}
        </router-link>

        <!-- Language Switcher -->
        <div class="px-3 py-3 border-t border-gray-100 mt-2">
          <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">{{ $t('nav.user') === 'Utilisateur' ? 'Langue' : 'Language' }}</p>
          <div class="flex gap-2">
            <button
              v-for="lang in languages"
              :key="lang.code"
              @click="changeLanguage(lang.code)"
              class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium"
              :class="currentLocale === lang.code ? 'bg-pomodoro-red text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
            >
              <span>{{ lang.flag }}</span>
              <span>{{ lang.name }}</span>
            </button>
          </div>
        </div>

        <!-- Logout Button -->
        <div class="px-3 py-3 border-t border-gray-100 mt-2">
          <button
            @click="handleMobileLogout"
            class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-md text-base font-medium text-red-600 hover:bg-red-50"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            {{ $t('nav.logout') }}
          </button>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '../stores/auth'
import { useAuth } from '../composables/useAuth'
import LanguageSwitcher from './LanguageSwitcher.vue'

const route = useRoute()
const authStore = useAuthStore()
const { logout: handleLogout } = useAuth()
const { locale } = useI18n()

const isMobileMenuOpen = ref(false)

const languages = [
  { code: 'en', name: 'English', flag: '🇬🇧' },
  { code: 'fr', name: 'Français', flag: '🇫🇷' }
]

const currentLocale = computed(() => locale.value)

const isActive = (path) => {
  return route.path === path
}

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
}

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false
}

const changeLanguage = (lang) => {
  locale.value = lang
  localStorage.setItem('language', lang)
}

const handleMobileLogout = () => {
  closeMobileMenu()
  handleLogout()
}
</script>

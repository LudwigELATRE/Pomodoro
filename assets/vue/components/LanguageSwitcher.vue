<template>
  <div class="relative">
    <button
      @click="toggleDropdown"
      class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
      </svg>
      <span class="uppercase">{{ currentLocale }}</span>
    </button>

    <div
      v-if="isOpen"
      class="absolute right-0 mt-2 w-32 bg-white rounded-md shadow-lg border border-gray-200 z-10"
    >
      <button
        v-for="lang in languages"
        :key="lang.code"
        @click="changeLanguage(lang.code)"
        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 flex items-center gap-2"
        :class="{ 'bg-gray-50 font-medium': currentLocale === lang.code }"
      >
        <span>{{ lang.flag }}</span>
        <span>{{ lang.name }}</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { locale } = useI18n()
const isOpen = ref(false)

const languages = [
  { code: 'en', name: 'English', flag: '🇬🇧' },
  { code: 'fr', name: 'Français', flag: '🇫🇷' }
]

const currentLocale = computed(() => locale.value)

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
}

const changeLanguage = (lang) => {
  locale.value = lang
  localStorage.setItem('language', lang)
  isOpen.value = false
}

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  const dropdown = event.target.closest('.relative')
  if (!dropdown) {
    isOpen.value = false
  }
}

// Load saved language on mount
const savedLanguage = localStorage.getItem('language')
if (savedLanguage) {
  locale.value = savedLanguage
}

// Add event listener for closing dropdown
if (typeof window !== 'undefined') {
  window.addEventListener('click', handleClickOutside)
}
</script>
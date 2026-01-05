import { createI18n } from 'vue-i18n'
import en from './locales/en.json'
import fr from './locales/fr.json'

// Get saved language from localStorage or default to 'en'
const savedLocale = localStorage.getItem('language') || 'en'

const i18n = createI18n({
  legacy: false,
  locale: savedLocale, // use saved language or default to 'en'
  fallbackLocale: 'en',
  messages: {
    en,
    fr
  }
})

export default i18n
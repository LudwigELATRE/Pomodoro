<template>
  <div class="min-h-screen bg-gray-50">
    <Navbar />

    <!-- Main Content -->
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900">{{ $t('settings.title') }}</h2>
        <p class="text-gray-600 mt-1">{{ $t('settings.subtitle') }}</p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-pomodoro-red"></div>
      </div>

      <!-- Settings Form -->
      <div v-else class="card">
        <!-- Success Message -->
        <div v-if="successMessage" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
          <p class="text-green-700 text-sm">{{ successMessage }}</p>
        </div>

        <!-- Error Message -->
        <div v-if="errorMessage" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
          <p class="text-red-700 text-sm">{{ errorMessage }}</p>
        </div>

        <form @submit.prevent="handleSave" class="space-y-6">
          <!-- Work Duration -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              {{ $t('settings.workDuration') }}
            </label>
            <div class="flex items-center gap-4">
              <input
                v-model.number="formData.workDuration"
                type="range"
                min="300"
                max="3600"
                step="60"
                class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-pomodoro-red"
              />
              <div class="w-24 text-right">
                <span class="text-lg font-semibold text-gray-900">
                  {{ formatMinutes(formData.workDuration) }}
                </span>
                <span class="text-sm text-gray-500 ml-1">{{ $t('settings.minutes') }}</span>
              </div>
            </div>
            <p class="text-xs text-gray-500 mt-1">{{ $t('settings.workDurationDesc') }}</p>
          </div>

          <!-- Short Break Duration -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              {{ $t('settings.shortBreakDuration') }}
            </label>
            <div class="flex items-center gap-4">
              <input
                v-model.number="formData.shortBreakDuration"
                type="range"
                min="60"
                max="900"
                step="60"
                class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-500"
              />
              <div class="w-24 text-right">
                <span class="text-lg font-semibold text-gray-900">
                  {{ formatMinutes(formData.shortBreakDuration) }}
                </span>
                <span class="text-sm text-gray-500 ml-1">{{ $t('settings.minutes') }}</span>
              </div>
            </div>
            <p class="text-xs text-gray-500 mt-1">{{ $t('settings.shortBreakDurationDesc') }}</p>
          </div>

          <!-- Long Break Duration -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              {{ $t('settings.longBreakDuration') }}
            </label>
            <div class="flex items-center gap-4">
              <input
                v-model.number="formData.longBreakDuration"
                type="range"
                min="600"
                max="1800"
                step="60"
                class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-green-500"
              />
              <div class="w-24 text-right">
                <span class="text-lg font-semibold text-gray-900">
                  {{ formatMinutes(formData.longBreakDuration) }}
                </span>
                <span class="text-sm text-gray-500 ml-1">{{ $t('settings.minutes') }}</span>
              </div>
            </div>
            <p class="text-xs text-gray-500 mt-1">{{ $t('settings.longBreakDurationDesc') }}</p>
          </div>

          <!-- Pomodoros Until Long Break -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              {{ $t('settings.pomodorosUntilLongBreak') }}
            </label>
            <div class="flex items-center gap-4">
              <select
                v-model.number="formData.pomodorosUntilLongBreak"
                class="input flex-1"
              >
                <option :value="2">2 {{ $t('settings.pomodoros') }}</option>
                <option :value="3">3 {{ $t('settings.pomodoros') }}</option>
                <option :value="4">4 {{ $t('settings.pomodoros') }}</option>
                <option :value="5">5 {{ $t('settings.pomodoros') }}</option>
                <option :value="6">6 {{ $t('settings.pomodoros') }}</option>
                <option :value="8">8 {{ $t('settings.pomodoros') }}</option>
              </select>
            </div>
            <p class="text-xs text-gray-500 mt-1">{{ $t('settings.pomodorosDesc') }}</p>
          </div>

          <!-- Preview -->
          <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">{{ $t('settings.preview') }}</h3>
            <div class="space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-600">{{ $t('settings.workSession') }}:</span>
                <span class="font-medium text-gray-900">{{ formatMinutes(formData.workDuration) }} {{ $t('settings.minutes') }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">{{ $t('settings.shortBreak') }}:</span>
                <span class="font-medium text-gray-900">{{ formatMinutes(formData.shortBreakDuration) }} {{ $t('settings.minutes') }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">{{ $t('settings.longBreak') }}:</span>
                <span class="font-medium text-gray-900">{{ formatMinutes(formData.longBreakDuration) }} {{ $t('settings.minutes') }}</span>
              </div>
              <div class="flex justify-between pt-2 border-t border-gray-300">
                <span class="text-gray-600">{{ $t('settings.cycleLength') }}:</span>
                <span class="font-medium text-gray-900">{{ cycleLength }} {{ $t('settings.minutes') }}</span>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-3 pt-4">
            <button
              type="submit"
              :disabled="saving"
              class="btn-primary flex-1"
            >
              {{ saving ? $t('settings.saving') : $t('settings.save') }}
            </button>
            <button
              type="button"
              @click="handleReset"
              class="btn-secondary"
            >
              {{ $t('settings.resetToDefault') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { usePomodoroStore } from '../stores/pomodoro'
import Navbar from '../components/Navbar.vue'

const pomodoroStore = usePomodoroStore()

const loading = ref(false)
const saving = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const formData = ref({
  workDuration: 1500,
  shortBreakDuration: 300,
  longBreakDuration: 900,
  pomodorosUntilLongBreak: 4
})

const defaultSettings = {
  workDuration: 1500,
  shortBreakDuration: 300,
  longBreakDuration: 900,
  pomodorosUntilLongBreak: 4
}

const cycleLength = computed(() => {
  const workTime = formData.value.workDuration * formData.value.pomodorosUntilLongBreak
  const shortBreaks = formData.value.shortBreakDuration * (formData.value.pomodorosUntilLongBreak - 1)
  const longBreak = formData.value.longBreakDuration
  const totalSeconds = workTime + shortBreaks + longBreak
  return Math.round(totalSeconds / 60)
})

function formatMinutes(seconds) {
  return Math.round(seconds / 60)
}

async function handleSave() {
  saving.value = true
  successMessage.value = ''
  errorMessage.value = ''

  try {
    await pomodoroStore.updateSettings(formData.value)
    successMessage.value = 'Settings saved successfully!'

    // Clear success message after 3 seconds
    setTimeout(() => {
      successMessage.value = ''
    }, 3000)
  } catch (err) {
    console.error('Failed to save settings:', err)
    errorMessage.value = 'Failed to save settings. Please try again.'
  } finally {
    saving.value = false
  }
}

function handleReset() {
  formData.value = { ...defaultSettings }
  successMessage.value = ''
  errorMessage.value = ''
}

onMounted(async () => {
  loading.value = true
  try {
    await pomodoroStore.loadSettings()
    formData.value = { ...pomodoroStore.settings }
  } catch (err) {
    console.error('Failed to load settings:', err)
    errorMessage.value = 'Failed to load settings. Using defaults.'
  } finally {
    loading.value = false
  }
})
</script>

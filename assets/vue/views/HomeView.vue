<template>
  <div class="min-h-screen bg-gray-50">
    <Navbar />

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Timer Section -->
        <div class="lg:col-span-2">
          <PomodoroTimer />
        </div>

        <!-- Today's Sessions -->
        <div class="lg:col-span-1">
          <div class="card">
            <h2 class="text-xl font-bold text-gray-900 mb-4">{{ $t('timer.todaySessions') }}</h2>

            <div v-if="loading" class="text-center py-8">
              <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-pomodoro-red"></div>
            </div>

            <div v-else-if="pomodoroStore.todaySessions.length === 0" class="text-center py-8">
              <p class="text-gray-500">{{ $t('timer.noSessionsYet') }}</p>
              <p class="text-sm text-gray-400 mt-1">{{ $t('timer.startFirstPomodoro') }}</p>
            </div>

            <div v-else class="space-y-3 max-h-96 overflow-y-auto">
              <div
                v-for="session in pomodoroStore.todaySessions"
                :key="session.id"
                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
              >
                <div class="flex items-center gap-3">
                  <div
                    :class="[
                      'w-3 h-3 rounded-full',
                      session.type === 'work' ? 'bg-pomodoro-red' :
                      session.type === 'short_break' ? 'bg-blue-500' :
                      'bg-green-500'
                    ]"
                  ></div>
                  <div>
                    <p class="text-sm font-medium text-gray-900">
                      {{ getSessionTypeName(session.type) }}
                    </p>
                    <p class="text-xs text-gray-500">
                      {{ formatTime(session.startTime) }}
                    </p>
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <span class="text-sm text-gray-600">{{ formatDuration(session.duration) }}</span>
                  <span
                    v-if="session.completed"
                    class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded"
                  >
                    ✓
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { usePomodoroStore } from '../stores/pomodoro'
import { useI18n } from 'vue-i18n'
import Navbar from '../components/Navbar.vue'
import PomodoroTimer from '../components/pomodoro/PomodoroTimer.vue'

const pomodoroStore = usePomodoroStore()
const { t } = useI18n()
const loading = ref(false)

function getSessionTypeName(type) {
  switch (type) {
    case 'work':
      return t('timer.workSession')
    case 'short_break':
      return t('timer.shortBreak')
    case 'long_break':
      return t('timer.longBreak')
    default:
      return type
  }
}

function formatTime(timestamp) {
  if (!timestamp) {
    return '--:--'
  }
  const date = new Date(timestamp)
  if (isNaN(date.getTime())) {
    return '--:--'
  }
  return date.toLocaleTimeString('fr-FR', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

function formatDuration(seconds) {
  // Vérifier que seconds est un nombre valide
  const num = Number(seconds)
  if (isNaN(num) || num < 0) {
    return '0' + t('timer.min')
  }
  const minutes = Math.floor(num / 60)
  return `${minutes}${t('timer.min')}`
}

onMounted(async () => {
  loading.value = true
  try {
    await Promise.all([
      pomodoroStore.loadSettings(),
      pomodoroStore.loadTodaySessions()
    ])
  } catch (err) {
    console.error('Failed to load data:', err)
  } finally {
    loading.value = false
  }
})
</script>

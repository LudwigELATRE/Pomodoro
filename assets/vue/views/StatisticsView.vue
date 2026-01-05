<template>
  <div class="min-h-screen bg-gray-50">
    <Navbar />

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900">{{ $t('statistics.title') }}</h2>
        <p class="text-gray-600 mt-1">{{ $t('statistics.subtitle') }}</p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-pomodoro-red"></div>
      </div>

      <!-- Statistics Cards -->
      <div v-else-if="pomodoroStore.statistics" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Sessions -->
        <div class="card">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600 mb-1">{{ $t('statistics.totalSessions') }}</p>
              <p class="text-4xl font-bold text-gray-900">
                {{ pomodoroStore.statistics.totalSessions || 0 }}
              </p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
              </svg>
            </div>
          </div>
          <p class="text-xs text-gray-500 mt-4">{{ $t('statistics.allTimeSessions') }}</p>
        </div>

        <!-- Completed Sessions -->
        <div class="card">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600 mb-1">{{ $t('statistics.completedSessions') }}</p>
              <p class="text-4xl font-bold text-gray-900">
                {{ pomodoroStore.statistics.completedSessions || 0 }}
              </p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
          <p class="text-xs text-gray-500 mt-4">
            {{ completionRate }}% {{ $t('statistics.completionRate') }}
          </p>
        </div>

        <!-- Total Time -->
        <div class="card">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600 mb-1">{{ $t('statistics.totalTime') }}</p>
              <p class="text-4xl font-bold text-gray-900">
                {{ totalTimeFormatted }}
              </p>
            </div>
            <div class="w-12 h-12 bg-pomodoro-red/10 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-pomodoro-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
          <p class="text-xs text-gray-500 mt-4">{{ $t('statistics.hoursOfWork') }}</p>
        </div>

        <!-- Today's Sessions -->
        <div class="card">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600 mb-1">{{ $t('statistics.todaySessions') }}</p>
              <p class="text-4xl font-bold text-gray-900">
                {{ pomodoroStore.statistics.todaySessions || 0 }}
              </p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
            </div>
          </div>
          <p class="text-xs text-gray-500 mt-4">{{ $t('statistics.sessionsCompletedToday') }}</p>
        </div>

        <!-- This Week -->
        <div class="card">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600 mb-1">{{ $t('statistics.weekSessions') }}</p>
              <p class="text-4xl font-bold text-gray-900">
                {{ pomodoroStore.statistics.weekSessions || 0 }}
              </p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
            </div>
          </div>
          <p class="text-xs text-gray-500 mt-4">{{ $t('statistics.sessionsThisWeek') }}</p>
        </div>

        <!-- Average per Day -->
        <div class="card">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600 mb-1">{{ $t('statistics.dailyAverage') }}</p>
              <p class="text-4xl font-bold text-gray-900">
                {{ averagePerDay }}
              </p>
            </div>
            <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
              </svg>
            </div>
          </div>
          <p class="text-xs text-gray-500 mt-4">{{ $t('statistics.averagePerDay') }}</p>
        </div>
      </div>

      <!-- No Data State -->
      <div v-else class="text-center py-12">
        <div class="text-gray-400 mb-4">
          <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
          </svg>
        </div>
        <p class="text-gray-600 text-lg">{{ $t('statistics.noData') }}</p>
        <p class="text-gray-500 text-sm mt-2">{{ $t('statistics.startFirstSession') }}</p>
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

const completionRate = computed(() => {
  if (!pomodoroStore.statistics) return 0

  const total = pomodoroStore.statistics.totalSessions || 0
  const completed = pomodoroStore.statistics.completedSessions || 0

  if (total === 0) return 0

  return Math.round((completed / total) * 100)
})

const totalTimeFormatted = computed(() => {
  if (!pomodoroStore.statistics) return '0h'

  const totalMinutes = Math.floor((pomodoroStore.statistics.totalTime || 0) / 60)
  const hours = Math.floor(totalMinutes / 60)
  const minutes = totalMinutes % 60

  if (hours > 0) {
    return minutes > 0 ? `${hours}h ${minutes}m` : `${hours}h`
  }

  return `${minutes}m`
})

const averagePerDay = computed(() => {
  if (!pomodoroStore.statistics) return 0

  return pomodoroStore.statistics.averagePerDay || 0
})

onMounted(async () => {
  loading.value = true
  try {
    await pomodoroStore.loadStatistics()
  } catch (err) {
    console.error('Failed to load statistics:', err)
  } finally {
    loading.value = false
  }
})
</script>

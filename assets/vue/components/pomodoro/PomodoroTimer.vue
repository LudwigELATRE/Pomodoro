<template>
  <div class="card">
    <div class="flex flex-col items-center">
      <!-- Session Type Indicator -->
      <div class="mb-4">
        <span
          :class="[
            'px-4 py-2 rounded-full text-sm font-semibold',
            sessionTypeColor
          ]"
        >
          {{ sessionTypeName }}
        </span>
      </div>

      <!-- Circular Progress -->
      <div class="relative mb-6">
        <svg class="transform -rotate-90" width="300" height="300">
          <!-- Background circle -->
          <circle
            cx="150"
            cy="150"
            r="130"
            stroke="#E5E7EB"
            stroke-width="12"
            fill="none"
          />
          <!-- Progress circle -->
          <circle
            cx="150"
            cy="150"
            r="130"
            :stroke="progressColor"
            stroke-width="12"
            fill="none"
            :stroke-dasharray="circleCircumference"
            :stroke-dashoffset="circleOffset"
            stroke-linecap="round"
            class="transition-all duration-1000 ease-linear"
          />
        </svg>

        <!-- Time Display -->
        <div class="absolute inset-0 flex items-center justify-center">
          <div class="text-center">
            <div class="text-6xl font-bold text-gray-900">
              {{ displayTime }}
            </div>
            <div class="text-sm text-gray-500 mt-2">
              Pomodoro {{ pomodoroStore.pomodoroCount }}/{{ pomodoroStore.settings.pomodorosUntilLongBreak }}
            </div>
          </div>
        </div>
      </div>

      <!-- Control Buttons -->
      <div class="flex gap-3 mb-4">
        <!-- Start Button -->
        <button
          v-if="!pomodoroStore.currentSession"
          @click="handleStart"
          class="btn-primary px-8"
        >
          {{ $t('timer.start') }}
        </button>

        <!-- Pause/Resume Button -->
        <button
          v-else-if="pomodoroStore.isRunning"
          @click="handlePause"
          class="btn-secondary px-8"
        >
          {{ $t('timer.pause') }}
        </button>
        <button
          v-else
          @click="handleResume"
          class="btn-primary px-8"
        >
          {{ $t('timer.resume') }}
        </button>

        <!-- Stop Button -->
        <button
          v-if="pomodoroStore.currentSession"
          @click="handleStop"
          class="btn-secondary px-6"
        >
          {{ $t('timer.stop') }}
        </button>

        <!-- Skip Button -->
        <button
          v-if="pomodoroStore.currentSession"
          @click="handleSkip"
          class="btn-secondary px-6"
        >
          {{ $t('timer.skip') }}
        </button>
      </div>

      <!-- Session Info -->
      <div v-if="pomodoroStore.currentSession" class="text-center text-sm text-gray-600">
        <p>{{ sessionDescription }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onUnmounted } from 'vue'
import { usePomodoroStore } from '../../stores/pomodoro'
import { useI18n } from 'vue-i18n'

const pomodoroStore = usePomodoroStore()
const { t } = useI18n()
let timerInterval = null

// Computed properties
const displayTime = computed(() => {
  const minutes = Math.floor(pomodoroStore.timeLeft / 60)
  const seconds = pomodoroStore.timeLeft % 60
  return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`
})

const sessionTypeName = computed(() => {
  if (!pomodoroStore.currentSession) return t('timer.readyToStart')

  switch (pomodoroStore.currentSession.type) {
    case 'work':
      return t('timer.workSession')
    case 'short_break':
      return t('timer.shortBreak')
    case 'long_break':
      return t('timer.longBreak')
    default:
      return 'Session'
  }
})

const sessionTypeColor = computed(() => {
  if (!pomodoroStore.currentSession) return 'bg-gray-100 text-gray-700'

  switch (pomodoroStore.currentSession.type) {
    case 'work':
      return 'bg-red-100 text-red-700'
    case 'short_break':
      return 'bg-blue-100 text-blue-700'
    case 'long_break':
      return 'bg-green-100 text-green-700'
    default:
      return 'bg-gray-100 text-gray-700'
  }
})

const progressColor = computed(() => {
  if (!pomodoroStore.currentSession) return '#E5E7EB'

  switch (pomodoroStore.currentSession.type) {
    case 'work':
      return '#DC2626'
    case 'short_break':
      return '#3B82F6'
    case 'long_break':
      return '#10B981'
    default:
      return '#E5E7EB'
  }
})

const sessionDescription = computed(() => {
  if (!pomodoroStore.currentSession) return ''

  switch (pomodoroStore.currentSession.type) {
    case 'work':
      return t('timer.focusOnTask')
    case 'short_break':
      return t('timer.takeShortBreak')
    case 'long_break':
      return t('timer.takeLongBreak')
    default:
      return ''
  }
})

// Circle progress calculations
const circleCircumference = 2 * Math.PI * 130
const circleOffset = computed(() => {
  if (!pomodoroStore.currentSession) return circleCircumference

  const progress = pomodoroStore.timeLeft / pomodoroStore.currentSession.duration
  return circleCircumference * (1 - progress)
})

// Timer functions
function startTimer() {
  if (timerInterval) {
    clearInterval(timerInterval)
  }

  timerInterval = setInterval(() => {
    if (pomodoroStore.timeLeft > 0) {
      pomodoroStore.timeLeft--
    } else {
      handleTimerComplete()
    }
  }, 1000)
}

function stopTimer() {
  if (timerInterval) {
    clearInterval(timerInterval)
    timerInterval = null
  }
}

async function handleTimerComplete() {
  stopTimer()

  // Play sound notification
  playNotificationSound()

  // Show browser notification
  showBrowserNotification()

  // Save session if it's a work session
  if (pomodoroStore.currentSession.type === 'work') {
    try {
      const session = await pomodoroStore.createSession({
        type: 'work',
        duration: pomodoroStore.currentSession.duration,
        startTime: pomodoroStore.currentSession.startTime,
        endTime: new Date().toISOString(),
        completed: true
      })

      // Increment pomodoro count
      pomodoroStore.pomodoroCount++

      // Mark as completed
      if (session?.id) {
        await pomodoroStore.completeSession(session.id)
      }
    } catch (err) {
      console.error('Failed to save session:', err)
    }
  }

  // Auto-start next session
  pomodoroStore.startPomodoro()
  startTimer()
}

function playNotificationSound() {
  try {
    // Simple beep sound using data URL
    const audioContext = new (window.AudioContext || window.webkitAudioContext)()
    const oscillator = audioContext.createOscillator()
    const gainNode = audioContext.createGain()

    oscillator.connect(gainNode)
    gainNode.connect(audioContext.destination)

    oscillator.frequency.value = 800
    oscillator.type = 'sine'

    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime)
    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5)

    oscillator.start(audioContext.currentTime)
    oscillator.stop(audioContext.currentTime + 0.5)
  } catch (err) {
    console.error('Failed to play sound:', err)
  }
}

function showBrowserNotification() {
  if ('Notification' in window && Notification.permission === 'granted') {
    const title = pomodoroStore.currentSession.type === 'work'
      ? t('timer.workSessionComplete')
      : t('timer.breakTimeOver')

    const body = pomodoroStore.currentSession.type === 'work'
      ? t('timer.greatJobBreak')
      : t('timer.breakOverFocus')

    new Notification(title, {
      body,
      icon: '/favicon.ico',
      badge: '/favicon.ico'
    })
  } else if ('Notification' in window && Notification.permission !== 'denied') {
    Notification.requestPermission()
  }
}

// Action handlers
function handleStart() {
  // Request notification permission
  if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission()
  }

  pomodoroStore.startPomodoro()
  startTimer()
}

function handlePause() {
  pomodoroStore.pausePomodoro()
  stopTimer()
}

function handleResume() {
  pomodoroStore.resumePomodoro()
  startTimer()
}

function handleStop() {
  pomodoroStore.stopPomodoro()
  stopTimer()
}

function handleSkip() {
  pomodoroStore.skipPomodoro()
  stopTimer()
}

// Watch for isRunning changes
watch(() => pomodoroStore.isRunning, (isRunning) => {
  if (isRunning) {
    startTimer()
  } else {
    stopTimer()
  }
})

// Cleanup on unmount
onUnmounted(() => {
  stopTimer()
})
</script>

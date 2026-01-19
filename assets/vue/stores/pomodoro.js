import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { pomodoroService } from '../services/pomodoro'

const STORAGE_KEY = 'pomodoro_session'

function saveSessionToStorage(data) {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(data))
}

function loadSessionFromStorage() {
  try {
    const saved = localStorage.getItem(STORAGE_KEY)
    if (saved) {
      return JSON.parse(saved)
    }
  } catch (e) {
    console.warn('Failed to load pomodoro session from localStorage:', e)
    localStorage.removeItem(STORAGE_KEY)
  }
  return null
}

function clearSessionStorage() {
  localStorage.removeItem(STORAGE_KEY)
}

export const usePomodoroStore = defineStore('pomodoro', () => {
  const sessions = ref([])
  const todaySessions = ref([])
  const statistics = ref(null)
  const settings = ref({
    workDuration: 1500,
    shortBreakDuration: 300,
    longBreakDuration: 900,
    pomodorosUntilLongBreak: 4,
  })

  // Load saved session from localStorage
  const savedSession = loadSessionFromStorage()

  const currentSession = ref(savedSession?.currentSession || null)
  const isRunning = ref(false) // Always start paused after reload
  const timeLeft = ref(savedSession?.timeLeft || 0)
  const pomodoroCount = ref(savedSession?.pomodoroCount || 0)

  const sessionType = computed(() => {
    if (!currentSession.value) return 'work'
    return currentSession.value.type
  })

  async function loadSettings() {
    try {
      const data = await pomodoroService.getSettings()
      console.log('Settings loaded from API:', data)
      if (data && typeof data === 'object') {
        settings.value = {
          workDuration: typeof data.workDuration === 'number' ? data.workDuration : 1500,
          shortBreakDuration: typeof data.shortBreakDuration === 'number' ? data.shortBreakDuration : 300,
          longBreakDuration: typeof data.longBreakDuration === 'number' ? data.longBreakDuration : 900,
          pomodorosUntilLongBreak: typeof data.pomodorosUntilLongBreak === 'number' ? data.pomodorosUntilLongBreak : 4,
        }
        console.log('Settings applied:', settings.value)
      }
    } catch (err) {
      console.error('Failed to load settings:', err)
    }
  }

  async function updateSettings(newSettings) {
    try {
      console.log('[PomodoroStore] Envoi à l\'API:', newSettings)
      const data = await pomodoroService.updateSettings(newSettings)
      console.log('[PomodoroStore] Réponse de l\'API:', data)

      // Mettre à jour directement avec les nouvelles valeurs
      // (on sait que l'API les sauvegarde correctement)
      settings.value = {
        workDuration: newSettings.workDuration ?? settings.value.workDuration,
        shortBreakDuration: newSettings.shortBreakDuration ?? settings.value.shortBreakDuration,
        longBreakDuration: newSettings.longBreakDuration ?? settings.value.longBreakDuration,
        pomodorosUntilLongBreak: newSettings.pomodorosUntilLongBreak ?? settings.value.pomodorosUntilLongBreak,
      }

      console.log('[PomodoroStore] Settings mis à jour:', settings.value)
    } catch (err) {
      console.error('[PomodoroStore] Erreur lors de la mise à jour:', err)
      throw err
    }
  }

  async function loadSessions() {
    try {
      const data = await pomodoroService.getSessions()
      sessions.value = Array.isArray(data) ? data : []
    } catch (err) {
      console.error('Failed to load sessions:', err)
      sessions.value = []
    }
  }

  async function loadTodaySessions() {
    try {
      const data = await pomodoroService.getTodaySessions()
      console.log('[PomodoroStore] Sessions du jour chargées:', data)

      // Filtrer les sessions invalides
      if (Array.isArray(data)) {
        todaySessions.value = data.filter(session => {
          // Vérifier que la session a les champs requis
          const isValid = session &&
                         session.type &&
                         session.duration != null &&
                         session.startTime

          if (!isValid) {
            console.warn('[PomodoroStore] Session invalide ignorée:', session)
          }
          return isValid
        })
      } else {
        todaySessions.value = []
      }
    } catch (err) {
      console.error('Failed to load today sessions:', err)
      todaySessions.value = []
    }
  }

  async function loadStatistics() {
    try {
      const data = await pomodoroService.getStatistics()
      statistics.value = data
    } catch (err) {
      console.error('Failed to load statistics:', err)
    }
  }

  async function createSession(session) {
    try {
      const data = await pomodoroService.createSession(session)
      sessions.value.unshift(data)
      todaySessions.value.unshift(data)
      return data
    } catch (err) {
      console.error('Failed to create session:', err)
      throw err
    }
  }

  async function completeSession(sessionId) {
    try {
      await pomodoroService.updateSession(sessionId, {
        completed: true,
        endTime: new Date().toISOString()
      })
      await loadTodaySessions()
      await loadStatistics()
    } catch (err) {
      console.error('Failed to complete session:', err)
    }
  }

  function startPomodoro() {
    const type = getNextSessionType()
    const duration = getDurationForType(type)

    console.log('[PomodoroStore] Démarrage d\'un nouveau pomodoro')
    console.log('[PomodoroStore] Type de session:', type)
    console.log('[PomodoroStore] Paramètres actuels:', settings.value)
    console.log('[PomodoroStore] Durée calculée:', duration, 'secondes')

    timeLeft.value = duration
    currentSession.value = {
      type,
      duration,
      startTime: new Date().toISOString(),
    }
    isRunning.value = true

    saveSessionToStorage({
      currentSession: currentSession.value,
      timeLeft: timeLeft.value,
      pomodoroCount: pomodoroCount.value,
    })
  }

  function updateTimeLeft(newTime) {
    timeLeft.value = newTime
    if (currentSession.value) {
      saveSessionToStorage({
        currentSession: currentSession.value,
        timeLeft: newTime,
        pomodoroCount: pomodoroCount.value,
      })
    }
  }

  function pausePomodoro() {
    isRunning.value = false
  }

  function resumePomodoro() {
    isRunning.value = true
  }

  function stopPomodoro(resetCount = false) {
    isRunning.value = false
    currentSession.value = null
    timeLeft.value = 0
    if (resetCount) {
      pomodoroCount.value = 0
    }
    clearSessionStorage()
  }

  function skipPomodoro() {
    if (currentSession.value?.type === 'work') {
      pomodoroCount.value++
    }
    stopPomodoro()
  }

  function completeCurrentSession() {
    if (currentSession.value?.type === 'work') {
      pomodoroCount.value++
    }
    saveSessionToStorage({
      currentSession: null,
      timeLeft: 0,
      pomodoroCount: pomodoroCount.value,
    })
  }

  function getNextSessionType() {
    if (!currentSession.value) return 'work'

    if (currentSession.value.type === 'work') {
      // After this work session completes, pomodoroCount will be incremented
      // So we check if (pomodoroCount + 1) is a multiple of pomodorosUntilLongBreak
      const nextCount = pomodoroCount.value + 1
      if (nextCount > 0 && nextCount % settings.value.pomodorosUntilLongBreak === 0) {
        return 'long_break'
      }
      return 'short_break'
    }

    return 'work'
  }

  function getDurationForType(type) {
    switch (type) {
      case 'work':
        return settings.value.workDuration
      case 'short_break':
        return settings.value.shortBreakDuration
      case 'long_break':
        return settings.value.longBreakDuration
      default:
        return settings.value.workDuration
    }
  }

  return {
    sessions,
    todaySessions,
    statistics,
    settings,
    currentSession,
    isRunning,
    timeLeft,
    pomodoroCount,
    sessionType,
    loadSettings,
    updateSettings,
    loadSessions,
    loadTodaySessions,
    loadStatistics,
    createSession,
    completeSession,
    startPomodoro,
    pausePomodoro,
    resumePomodoro,
    stopPomodoro,
    skipPomodoro,
    getNextSessionType,
    getDurationForType,
    updateTimeLeft,
    completeCurrentSession,
  }
})

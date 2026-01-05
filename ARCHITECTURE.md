# 🍅 Architecture de l'Application Pomodoro

> Documentation complète de la structure et du fonctionnement de l'application

---

## 📋 Table des Matières

1. [Vue d'ensemble](#vue-densemble)
2. [Stack Technique](#stack-technique)
3. [Structure du Projet](#structure-du-projet)
4. [Backend (Symfony)](#backend-symfony)
5. [Frontend (Vue.js)](#frontend-vuejs)
6. [Base de Données](#base-de-données)
7. [Flux de Données](#flux-de-données)
8. [API Endpoints](#api-endpoints)
9. [Configuration](#configuration)

---

## Vue d'ensemble

Application Pomodoro monolithique combinant :
- **Backend** : API REST Symfony 7.0
- **Frontend** : SPA Vue.js 3 (Composition API)
- **Base de données** : SQLite (développement)
- **Authentification** : JWT + OAuth2 Google

### Architecture Globale

```
┌─────────────────────────────────────────────────────────┐
│                    NAVIGATEUR                            │
│  ┌───────────────────────────────────────────────────┐  │
│  │           Vue.js SPA (Frontend)                   │  │
│  │  - Composants Vue                                 │  │
│  │  - Pinia Stores                                   │  │
│  │  - Vue Router                                     │  │
│  │  - Axios (HTTP Client)                            │  │
│  └──────────────────┬────────────────────────────────┘  │
└─────────────────────┼────────────────────────────────────┘
                      │ HTTP/JSON
                      │ (API REST)
┌─────────────────────▼────────────────────────────────────┐
│              Symfony Backend (API)                        │
│  ┌────────────────────────────────────────────────────┐  │
│  │  Controllers (API Endpoints)                       │  │
│  │    ├── AuthController (JWT)                        │  │
│  │    ├── GoogleController (OAuth2)                   │  │
│  │    ├── PomodoroController                          │  │
│  │    ├── SettingsController                          │  │
│  │    └── StatisticsController                        │  │
│  └────────────────┬───────────────────────────────────┘  │
│                   │                                       │
│  ┌────────────────▼───────────────────────────────────┐  │
│  │  Doctrine ORM                                      │  │
│  │    ├── User Entity                                 │  │
│  │    ├── UserSettings Entity                         │  │
│  │    └── PomodoroSession Entity                      │  │
│  └────────────────┬───────────────────────────────────┘  │
└───────────────────┼─────────────────────────────────────┘
                    │
┌───────────────────▼─────────────────────────────────────┐
│              SQLite Database                             │
│  Tables:                                                 │
│    - users                                               │
│    - user_settings                                       │
│    - pomodoro_sessions                                   │
│    - messenger_messages                                  │
└─────────────────────────────────────────────────────────┘
```

---

## Stack Technique

### Backend
- **Framework** : Symfony 7.0
- **PHP** : >= 8.2
- **ORM** : Doctrine 3.5
- **Authentification** :
  - `lexik/jwt-authentication-bundle` (JWT tokens)
  - `knpuniversity/oauth2-client-bundle` (OAuth2)
  - `league/oauth2-google` (Provider Google)
- **CORS** : `nelmio/cors-bundle`

### Frontend
- **Framework** : Vue.js 3 (Composition API avec `<script setup>`)
- **State Management** : Pinia
- **Routing** : Vue Router
- **HTTP Client** : Axios
- **Internationalisation** : vue-i18n
- **Styles** : Tailwind CSS
- **Build** : Webpack Encore

### Base de Données
- **Développement** : SQLite
- **Production** : PostgreSQL (configurable)

---

## Structure du Projet

```
pomodoro-monolith/
├── assets/                      # Frontend Vue.js
│   ├── vue/
│   │   ├── components/         # Composants Vue
│   │   │   ├── Navbar.vue
│   │   │   ├── LanguageSwitcher.vue
│   │   │   └── pomodoro/
│   │   │       └── PomodoroTimer.vue    # ⏱️ Composant principal du timer
│   │   │
│   │   ├── views/              # Pages/Vues
│   │   │   ├── HomeView.vue              # 🏠 Page d'accueil avec timer
│   │   │   ├── SettingsView.vue          # ⚙️ Configuration du timer
│   │   │   ├── StatisticsView.vue        # 📊 Statistiques
│   │   │   ├── LoginView.vue             # 🔐 Connexion
│   │   │   └── AuthCallback.vue          # OAuth callback
│   │   │
│   │   ├── stores/             # Pinia Stores (État global)
│   │   │   ├── pomodoro.js               # 📦 Store principal
│   │   │   └── auth.js                   # 🔑 Store authentification
│   │   │
│   │   ├── services/           # Services API
│   │   │   ├── api.js                    # 🌐 Client Axios configuré
│   │   │   ├── pomodoro.js               # API Pomodoro/Settings
│   │   │   └── auth.js                   # API Auth
│   │   │
│   │   ├── router/             # Vue Router
│   │   │   └── index.js                  # Routes de l'app
│   │   │
│   │   ├── i18n/               # Internationalisation
│   │   │   ├── index.js
│   │   │   └── locales/
│   │   │       ├── fr.json               # 🇫🇷 Traductions françaises
│   │   │       └── en.json               # 🇬🇧 Traductions anglaises
│   │   │
│   │   └── App.vue             # Composant racine
│   │
│   └── styles/                 # CSS/Tailwind
│
├── src/                        # Backend Symfony
│   ├── Controller/             # Contrôleurs API
│   │   ├── AuthController.php            # POST /api/auth/login, /register
│   │   ├── GoogleController.php          # OAuth2 Google
│   │   ├── PomodoroController.php        # CRUD sessions pomodoro
│   │   ├── SettingsController.php        # 🎯 GET/PUT /api/settings
│   │   ├── StatisticsController.php      # GET /api/statistics
│   │   └── HomeController.php            # Page d'accueil (rendu Vue)
│   │
│   ├── Entity/                 # Entités Doctrine
│   │   ├── User.php                      # 👤 Utilisateur
│   │   ├── UserSettings.php              # ⚙️ Paramètres du timer
│   │   └── PomodoroSession.php           # 📝 Session pomodoro
│   │
│   └── Repository/             # Repositories Doctrine
│       ├── UserRepository.php
│       ├── UserSettingsRepository.php
│       └── PomodoroSessionRepository.php
│
├── config/                     # Configuration Symfony
│   ├── packages/
│   │   ├── doctrine.yaml                 # Config base de données
│   │   ├── security.yaml                 # Sécurité + JWT
│   │   ├── lexik_jwt_authentication.yaml # JWT config
│   │   ├── knpu_oauth2_client.yaml       # 🆕 OAuth2 Google
│   │   └── nelmio_cors.yaml              # CORS
│   │
│   ├── routes/
│   └── bundles.php                       # Bundles enregistrés
│
├── migrations/                 # Migrations Doctrine (vide = tables existent déjà)
│
├── public/                     # Point d'entrée web
│   └── index.php
│
├── templates/                  # Templates Twig
│   └── base.html.twig                    # Template de base (charge Vue)
│
├── var/                        # Cache, logs, data
│   └── data_dev.db                       # 🗄️ Base de données SQLite
│
├── .env                        # Variables d'environnement
├── composer.json               # Dépendances PHP
├── package.json                # Dépendances JS
└── webpack.config.js           # Config Webpack Encore
```

---

## Backend (Symfony)

### Entités Doctrine

#### 1. **User** (`src/Entity/User.php`)
```php
User {
    id: int
    email: string (unique)
    name: string
    password: string (nullable si OAuth)
    googleId: string (nullable)
    avatarUrl: string (nullable)
    authProvider: string (local|google)
    settings: UserSettings (OneToOne)
    pomodoroSessions: PomodoroSession[] (OneToMany)
}
```

#### 2. **UserSettings** (`src/Entity/UserSettings.php`)
```php
UserSettings {
    id: int
    user: User (OneToOne)
    workDuration: int = 1500        // 25 min en secondes
    shortBreakDuration: int = 300   // 5 min
    longBreakDuration: int = 900    // 15 min
    pomodorosUntilLongBreak: int = 4
}
```
> 🎯 **C'est ici que les paramètres du timer sont stockés en BDD**

#### 3. **PomodoroSession** (`src/Entity/PomodoroSession.php`)
```php
PomodoroSession {
    id: int
    user: User (ManyToOne)
    startTime: DateTimeImmutable
    endTime: DateTimeImmutable (nullable)
    duration: int (en secondes)
    type: string (work|short_break|long_break)
    completed: bool
}
```

### Contrôleurs API

#### **SettingsController** (`src/Controller/SettingsController.php`)
Le contrôleur clé pour les paramètres :

```php
// GET /api/settings
getSettings(): JsonResponse
  ↓
  1. Récupère l'utilisateur authentifié
  2. Charge ses settings depuis la BDD (ou crée par défaut)
  3. Retourne JSON: { workDuration, shortBreakDuration, ... }

// PUT /api/settings
updateSettings(Request): JsonResponse
  ↓
  1. Récupère l'utilisateur authentifié
  2. Parse le JSON du body
  3. Met à jour les champs de UserSettings
  4. Flush en BDD
  5. Retourne les settings mis à jour
```

#### **PomodoroController** (`src/Controller/PomodoroController.php`)
```php
POST   /api/pomodoros        → Créer une session
GET    /api/pomodoros        → Liste toutes les sessions
GET    /api/pomodoros/today  → Sessions du jour
PUT    /api/pomodoros/{id}   → Mettre à jour (complétion)
DELETE /api/pomodoros/{id}   → Supprimer
```

#### **GoogleController** (`src/Controller/GoogleController.php`)
```php
GET /connect/google           → Redirige vers Google OAuth
GET /connect/google/check     → Callback OAuth
  ↓
  1. Récupère les infos utilisateur Google
  2. Crée ou met à jour l'utilisateur
  3. Génère un JWT token
  4. Redirige vers le frontend avec le token
```

### Authentification

**Flow JWT** :
1. User login → `POST /api/auth/login` → Retourne JWT token
2. Frontend stocke token dans `localStorage`
3. Chaque requête inclut : `Authorization: Bearer {token}`
4. Symfony valide le token et identifie l'utilisateur

**Configuration** :
- Fichiers : `config/packages/security.yaml` + `lexik_jwt_authentication.yaml`
- Clés JWT : `config/jwt/private.pem` + `public.pem`

---

## Frontend (Vue.js)

### Architecture Vue 3

#### **Composition API avec `<script setup>`**
Tous les composants utilisent la syntaxe moderne :
```vue
<script setup>
import { ref, computed, onMounted } from 'vue'

const count = ref(0)
const doubled = computed(() => count.value * 2)
</script>
```

### Stores Pinia

#### **pomodoro.js** (`assets/vue/stores/pomodoro.js`)
Le store central de l'application :

```javascript
usePomodoroStore {
  // État
  settings: ref({
    workDuration: 1500,
    shortBreakDuration: 300,
    longBreakDuration: 900,
    pomodorosUntilLongBreak: 4
  }),
  currentSession: ref(null),
  isRunning: ref(false),
  timeLeft: ref(0),
  pomodoroCount: ref(0),
  sessions: ref([]),
  todaySessions: ref([]),

  // Actions clés
  loadSettings(),           // Charge depuis /api/settings
  updateSettings(data),     // Sauvegarde via PUT /api/settings
  startPomodoro(),          // Démarre un timer
  pausePomodoro(),
  stopPomodoro(),
  getDurationForType(type)  // Retourne la durée selon le type
}
```

**Flow des settings** :
```
1. App démarre
   ↓
2. HomeView.vue → onMounted()
   ↓
3. pomodoroStore.loadSettings()
   ↓
4. GET /api/settings (via pomodoroService)
   ↓
5. Settings stockés dans store.settings
   ↓
6. User clique "Start"
   ↓
7. startPomodoro() lit store.settings
   ↓
8. Utilise getDurationForType() pour calculer la durée
```

### Composants Principaux

#### **PomodoroTimer.vue** (`assets/vue/components/pomodoro/PomodoroTimer.vue`)
Le cœur de l'application :

```vue
- Affiche le timer circulaire
- Boutons Start/Pause/Stop/Skip
- Lit pomodoroStore.timeLeft pour l'affichage
- Décrémente timeLeft chaque seconde (setInterval)
- Quand timeLeft = 0 → handleTimerComplete()
  → Sauvegarde la session
  → Démarre automatiquement la session suivante
```

#### **SettingsView.vue** (`assets/vue/views/SettingsView.vue`)
Page de configuration :

```vue
- Sliders pour workDuration, shortBreakDuration, etc.
- formData (local) synchronisé avec pomodoroStore.settings
- handleSave():
  1. Appelle pomodoroStore.updateSettings(formData)
  2. Si timer en cours → Met à jour currentSession.duration
  3. Affiche message de succès en français
- Tous les messages utilisent vue-i18n ($t('settings.xxx'))
```

### Services API

#### **api.js** (`assets/vue/services/api.js`)
Client Axios configuré :
```javascript
axios.create({
  baseURL: '/api',  // Toutes les requêtes = /api/xxx
  headers: { 'Content-Type': 'application/json' }
})

// Intercepteur request : Ajoute JWT token
config.headers.Authorization = `Bearer ${token}`

// Intercepteur response : Si 401 → Logout
```

#### **pomodoro.js** (`assets/vue/services/pomodoro.js`)
Wrapper pour les endpoints :
```javascript
getSettings()        → GET /api/settings
updateSettings(data) → PUT /api/settings
createSession(data)  → POST /api/pomodoros
getSessions()        → GET /api/pomodoros
getTodaySessions()   → GET /api/pomodoros/today
getStatistics()      → GET /api/statistics
```

### Routing

#### **router/index.js** (`assets/vue/router/index.js`)
```javascript
Routes:
  / → HomeView           (timer + sessions du jour)
  /settings → SettingsView
  /statistics → StatisticsView
  /login → LoginView
  /auth/callback → AuthCallback (OAuth)
```

### Internationalisation

#### **i18n** (`assets/vue/i18n/`)
```javascript
Langues supportées: fr, en
Fichiers: locales/fr.json, locales/en.json

Utilisation:
  - Template: {{ $t('settings.title') }}
  - Script: const { t } = useI18n(); t('settings.title')
```

---

## Base de Données

### Tables SQLite

Base : `var/data_dev.db`

#### **users**
```sql
CREATE TABLE users (
  id INTEGER PRIMARY KEY,
  email VARCHAR(255) UNIQUE NOT NULL,
  name VARCHAR(255) NOT NULL,
  password VARCHAR(255),
  google_id VARCHAR(255),
  avatar_url VARCHAR(255),
  auth_provider VARCHAR(50)
);
```

#### **user_settings**
```sql
CREATE TABLE user_settings (
  id INTEGER PRIMARY KEY,
  user_id INTEGER NOT NULL UNIQUE,
  work_duration INTEGER DEFAULT 1500,
  short_break_duration INTEGER DEFAULT 300,
  long_break_duration INTEGER DEFAULT 900,
  pomodoros_until_long_break INTEGER DEFAULT 4,
  FOREIGN KEY (user_id) REFERENCES users(id)
);
```
> 🎯 **Table critique** : Stocke les paramètres du timer par utilisateur

#### **pomodoro_sessions**
```sql
CREATE TABLE pomodoro_sessions (
  id INTEGER PRIMARY KEY,
  user_id INTEGER NOT NULL,
  start_time DATETIME NOT NULL,
  end_time DATETIME,
  duration INTEGER NOT NULL,
  type VARCHAR(20) NOT NULL,
  completed BOOLEAN DEFAULT 0,
  FOREIGN KEY (user_id) REFERENCES users(id)
);
```

---

## Flux de Données

### 🔄 Chargement des Settings au Démarrage

```
┌─────────────────────────────────────────────────────────┐
│ 1. User ouvre l'app (/)                                 │
└────────────────────┬────────────────────────────────────┘
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 2. HomeView.vue → onMounted()                           │
│    pomodoroStore.loadSettings()                         │
└────────────────────┬────────────────────────────────────┘
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 3. pomodoroService.getSettings()                        │
│    → GET /api/settings                                  │
│      Headers: { Authorization: Bearer {token} }         │
└────────────────────┬────────────────────────────────────┘
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 4. Symfony SettingsController::getSettings()            │
│    - Vérifie JWT token → Identifie user                │
│    - $user->getSettings() → Query BDD user_settings     │
│    - Retourne JSON                                      │
└────────────────────┬────────────────────────────────────┘
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 5. Response: {                                          │
│      workDuration: 1500,                                │
│      shortBreakDuration: 300,                           │
│      longBreakDuration: 900,                            │
│      pomodorosUntilLongBreak: 4                         │
│    }                                                    │
└────────────────────┬────────────────────────────────────┘
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 6. pomodoroStore.settings = response.data               │
│    Les settings sont maintenant dans le store !        │
└─────────────────────────────────────────────────────────┘
```

### 💾 Sauvegarde des Settings

```
┌─────────────────────────────────────────────────────────┐
│ 1. User change workDuration à 30 min (1800s)            │
│    Clique sur "Enregistrer"                             │
└────────────────────┬────────────────────────────────────┘
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 2. SettingsView::handleSave()                           │
│    pomodoroStore.updateSettings({                       │
│      workDuration: 1800, ...                            │
│    })                                                   │
└────────────────────┬────────────────────────────────────┘
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 3. pomodoroService.updateSettings(data)                 │
│    → PUT /api/settings                                  │
│      Body: { workDuration: 1800, ... }                  │
└────────────────────┬────────────────────────────────────┘
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 4. Symfony SettingsController::updateSettings()         │
│    - Récupère user depuis JWT                           │
│    - $settings = $user->getSettings()                   │
│    - $settings->setWorkDuration(1800)                   │
│    - entityManager->flush()  → UPDATE user_settings     │
│    - Retourne settings mis à jour                       │
└────────────────────┬────────────────────────────────────┘
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 5. Frontend reçoit response                             │
│    pomodoroStore.settings = response.data               │
│    Store mis à jour avec nouvelles valeurs !            │
└────────────────────┬────────────────────────────────────┘
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 6. Si timer en cours (SettingsView):                    │
│    - Récupère nouvelle durée via getDurationForType()   │
│    - Met à jour currentSession.duration                 │
│    - Met à jour timeLeft                                │
│    → Timer reflète immédiatement le changement !        │
└─────────────────────────────────────────────────────────┘
```

### ⏱️ Démarrage d'un Timer

```
┌─────────────────────────────────────────────────────────┐
│ 1. User clique "Démarrer"                               │
└────────────────────┬────────────────────────────────────┘
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 2. PomodoroTimer::handleStart()                         │
│    pomodoroStore.startPomodoro()                        │
└────────────────────┬────────────────────────────────────┘
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 3. pomodoroStore.startPomodoro()                        │
│    - type = getNextSessionType()  // 'work'             │
│    - duration = getDurationForType('work')              │
│      → Lit settings.workDuration  // 1500s              │
│    - timeLeft = 1500                                    │
│    - currentSession = {                                 │
│        type: 'work',                                    │
│        duration: 1500,                                  │
│        startTime: '2025-11-30T10:00:00'                 │
│      }                                                  │
│    - isRunning = true                                   │
└────────────────────┬────────────────────────────────────┘
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 4. PomodoroTimer::startTimer()                          │
│    setInterval(() => {                                  │
│      if (timeLeft > 0) timeLeft--                       │
│      else handleTimerComplete()                         │
│    }, 1000)                                             │
└────────────────────┬────────────────────────────────────┘
                     ▼
┌─────────────────────────────────────────────────────────┐
│ 5. Affichage : 25:00, 24:59, 24:58...                   │
│    (computed displayTime lit pomodoroStore.timeLeft)    │
└─────────────────────────────────────────────────────────┘
```

---

## API Endpoints

### Authentification

| Méthode | URL | Description | Body | Response |
|---------|-----|-------------|------|----------|
| POST | `/api/auth/login` | Connexion | `{email, password}` | `{token, user}` |
| POST | `/api/auth/register` | Inscription | `{email, password, name}` | `{token, user}` |
| GET | `/connect/google` | Redirect OAuth Google | - | Redirect |
| GET | `/connect/google/check` | Callback OAuth | - | Redirect avec token |

### Settings

| Méthode | URL | Description | Body | Response |
|---------|-----|-------------|------|----------|
| GET | `/api/settings` | Récupère settings user | - | `{workDuration, shortBreakDuration, ...}` |
| PUT | `/api/settings` | Met à jour settings | `{workDuration?: int, ...}` | `{workDuration, ...}` |

### Pomodoro Sessions

| Méthode | URL | Description | Body | Response |
|---------|-----|-------------|------|----------|
| GET | `/api/pomodoros` | Liste toutes les sessions | - | `PomodoroSession[]` |
| GET | `/api/pomodoros/today` | Sessions du jour | - | `PomodoroSession[]` |
| POST | `/api/pomodoros` | Créer session | `{duration, type, startTime, ...}` | `PomodoroSession` |
| PUT | `/api/pomodoros/{id}` | Mettre à jour | `{completed?: bool, endTime?: string}` | `PomodoroSession` |
| DELETE | `/api/pomodoros/{id}` | Supprimer session | - | `{message}` |

### Statistiques

| Méthode | URL | Description | Response |
|---------|-----|-------------|----------|
| GET | `/api/statistics` | Stats globales | `{totalSessions, completedSessions, ...}` |

---

## Configuration

### Variables d'Environnement (`.env`)

```bash
# Symfony
APP_ENV=dev
APP_SECRET=xxx

# Base de données (SQLite par défaut)
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_dev.db"

# JWT
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=xxx

# CORS
CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'

# OAuth2 Google
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret

# Frontend
FRONTEND_URL=http://localhost:5173
```

### OAuth2 Google (`config/packages/knpu_oauth2_client.yaml`)

```yaml
knpu_oauth2_client:
    clients:
        google:
            type: google
            client_id: '%env(GOOGLE_CLIENT_ID)%'
            client_secret: '%env(GOOGLE_CLIENT_SECRET)%'
            redirect_route: connect_google_check
```

### CORS (`config/packages/nelmio_cors.yaml`)

Permet au frontend (port 5173) de communiquer avec l'API (port 8000).

---

## Débogage

### Logs de Débogage Ajoutés

#### Console Browser (Frontend)

Tous les logs commencent par un préfixe pour faciliter le suivi :

```javascript
// Dans SettingsView.vue
[SettingsView] Chargement des paramètres...
[SettingsView] Paramètres chargés depuis le store: {...}
[SettingsView] Sauvegarde des paramètres: {...}
[SettingsView] Minuteur en cours détecté, mise à jour...

// Dans pomodoro.js (store)
[PomodoroStore] Démarrage d'un nouveau pomodoro
[PomodoroStore] Type de session: work
[PomodoroStore] Paramètres actuels: {workDuration: 1500, ...}
[PomodoroStore] Durée calculée: 1500 secondes

// Dans pomodoroService (déjà existant)
Settings loaded from API: {...}
Settings applied: {...}
```

### Vérifications à Faire

#### 1. **Base de données**
```bash
# Vérifier que les tables existent
php bin/console dbal:run-sql "SELECT name FROM sqlite_master WHERE type='table'"

# Vérifier un user et ses settings
php bin/console dbal:run-sql "SELECT * FROM user_settings"
```

#### 2. **Backend**
```bash
# Démarrer le serveur Symfony
symfony server:start

# Tester l'endpoint settings (remplacer {token})
curl -H "Authorization: Bearer {token}" http://localhost:8000/api/settings
```

#### 3. **Frontend**
```bash
# Compiler les assets
npm run dev

# Ouvrir la console du navigateur (F12)
# Vérifier les logs préfixés [SettingsView] et [PomodoroStore]
```

#### 4. **Flow complet**
1. Login → Récupérer token
2. Aller sur `/` → Vérifier `loadSettings()` dans console
3. Aller sur `/settings` → Changer une valeur
4. Sauvegarder → Vérifier logs de sauvegarde
5. Retourner sur `/` → Démarrer timer → Vérifier durée utilisée

---

## Points d'Attention ⚠️

### 1. **Settings Non Pris en Compte**

**Problème** : Timer utilise toujours 25 min même après changement.

**Causes possibles** :
- Settings non sauvegardés en BDD → Vérifier logs `[SettingsView]`
- Settings sauvegardés mais non rechargés → Vérifier `loadSettings()` dans console
- Timer utilise valeurs hardcodées → Vérifier logs `[PomodoroStore] Durée calculée`

**Solution** : Suivre les logs dans la console pour identifier où le flux est interrompu.

### 2. **Authentification**

**Rappel** : Tous les endpoints nécessitent un JWT token sauf :
- `/api/auth/login`
- `/api/auth/register`
- `/connect/google`

Si 401 Unauthorized → Token expiré ou manquant.

### 3. **OAuth2 Google**

**Configuration requise** :
1. Créer projet sur Google Cloud Console
2. Créer identifiants OAuth 2.0
3. Ajouter URL de redirection : `http://localhost:8000/connect/google/check`
4. Copier Client ID et Secret dans `.env`

---

## Commandes Utiles

```bash
# Backend
composer install                    # Installer dépendances PHP
php bin/console cache:clear         # Vider le cache
php bin/console doctrine:migrations:migrate  # Migrer BDD
symfony server:start                # Démarrer serveur (port 8000)

# Frontend
npm install                         # Installer dépendances JS
npm run dev                         # Compiler assets (mode watch)
npm run build                       # Build production

# Base de données
php bin/console doctrine:database:create    # Créer BDD
php bin/console make:migration              # Générer migration
php bin/console doctrine:migrations:migrate # Appliquer migrations
php bin/console dbal:run-sql "SELECT ..."  # Exécuter SQL

# Debug
php bin/console debug:router        # Liste des routes
php bin/console debug:container     # Liste des services
```

---

## Résumé du Flux Settings

```
╔═══════════════════════════════════════════════════════════════╗
║  STOCKAGE DES SETTINGS : BASE DE DONNÉES (user_settings)     ║
╠═══════════════════════════════════════════════════════════════╣
║                                                               ║
║  1. Au démarrage de l'app :                                   ║
║     HomeView → loadSettings() → GET /api/settings             ║
║     → Settings chargés en mémoire (pomodoroStore.settings)    ║
║                                                               ║
║  2. User change settings :                                    ║
║     SettingsView → updateSettings() → PUT /api/settings       ║
║     → BDD mise à jour                                         ║
║     → Store mis à jour                                        ║
║     → Si timer en cours : durée mise à jour instantanément    ║
║                                                               ║
║  3. User démarre timer :                                      ║
║     startPomodoro() → getDurationForType()                    ║
║     → Lit pomodoroStore.settings.workDuration                 ║
║     → Utilise la valeur de la BDD (chargée au step 1)         ║
║                                                               ║
╚═══════════════════════════════════════════════════════════════╝
```

---

**Généré le** : 2025-11-30
**Version** : 1.0
**Stack** : Symfony 7.0 + Vue.js 3 + SQLite

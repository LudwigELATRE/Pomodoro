# Pomodoro Timer Application

Application web Pomodoro complète avec backend Symfony et frontend Vue.js 3, incluant l'authentification JWT et Google OAuth.

## 🚀 Fonctionnalités

### Backend (Symfony)
- ✅ API REST complète
- ✅ Authentification JWT avec LexikJWTAuthenticationBundle
- ✅ Google OAuth 2.0 (Sign in with Google)
- ✅ CRUD sessions Pomodoro
- ✅ Statistiques utilisateur
- ✅ Paramètres personnalisés
- ✅ PostgreSQL comme base de données
- ✅ CORS configuré pour le frontend

### Frontend (Vue.js 3)
- ✅ Interface moderne avec Tailwind CSS
- ✅ Timer Pomodoro avec indicateur circulaire
- ✅ Authentification classique + Google Sign-In
- ✅ Gestion d'état avec Pinia
- ✅ Navigation protégée avec Vue Router
- ✅ Notifications sonores et navigateur
- ✅ Statistiques et historique
- ✅ Paramètres personnalisables

### Base de données
- ✅ SQLite (fichier local, aucune configuration nécessaire)
- ✅ Migrations Doctrine prêtes à l'emploi

## 📋 Prérequis

- Docker et Docker Compose (OU PHP 8.2+ et Node.js 20+ pour exécution locale)
- Un compte Google Cloud Platform (pour Google OAuth)
- Git

## 🔧 Configuration Google OAuth

### 1. Créer un projet Google Cloud

1. Allez sur [Google Cloud Console](https://console.cloud.google.com/)
2. Créez un nouveau projet ou sélectionnez-en un
3. Activez l'API Google+ :
   - Menu → APIs & Services → Library
   - Recherchez "Google+ API"
   - Cliquez sur "Enable"

### 2. Créer des credentials OAuth 2.0

1. Menu → APIs & Services → Credentials
2. Cliquez sur "Create Credentials" → "OAuth client ID"
3. Type d'application : "Web application"
4. Nom : "Pomodoro App"
5. Origines JavaScript autorisées :
   ```
   http://localhost:5173
   ```
6. URIs de redirection autorisés :
   ```
   http://localhost:8000
   ```
7. Cliquez sur "Create"
8. **Notez le Client ID et le Client Secret**

## ⚙️ Installation

### 1. Cloner le projet

```bash
git clone <votre-repo>
cd vuejs
```

### 2. Configuration Backend (.env)

Éditez le fichier `backend/.env` et ajoutez vos credentials Google :

```env
GOOGLE_CLIENT_ID=votre-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=votre-client-secret
```

Les autres variables sont déjà configurées pour Docker.

### 3. Configuration Frontend (.env)

Éditez le fichier `frontend/.env` et ajoutez votre Google Client ID :

```env
VITE_API_URL=http://localhost:8000
VITE_GOOGLE_CLIENT_ID=votre-client-id.apps.googleusercontent.com
```

### 4. Lancer l'application avec Docker

```bash
# Démarrer tous les services
docker-compose up -d

# Vérifier que les containers sont lancés
docker-compose ps
```

### 5. Initialiser la base de données SQLite

```bash
# Accéder au container backend
docker exec -it pomodoro_backend bash

# Créer le schéma de la base de données
php bin/console doctrine:schema:create

# Quitter le container
exit
```

### 6. Installer les dépendances frontend

```bash
# Accéder au container frontend
docker exec -it pomodoro_frontend sh

# Installer les dépendances
npm install

# Quitter le container
exit
```

## 🚀 Utilisation

### Accéder à l'application

- **Frontend** : http://localhost:5173
- **Backend API** : http://localhost:8000/api
- **Base de données SQLite** : backend/var/data.db

### Comptes de test

Créez un nouveau compte via l'interface ou utilisez Google Sign-In.

## 📡 Endpoints API

### Authentification

```bash
POST /api/register
Body: { "email": "user@example.com", "password": "password", "name": "John Doe" }

POST /api/login
Body: { "email": "user@example.com", "password": "password" }

POST /api/auth/google
Body: { "token": "google-id-token" }

GET /api/me
Headers: Authorization: Bearer {jwt-token}
```

### Sessions Pomodoro

```bash
POST /api/pomodoros
Headers: Authorization: Bearer {jwt-token}
Body: { "duration": 1500, "type": "work", "completed": true }

GET /api/pomodoros
Headers: Authorization: Bearer {jwt-token}

GET /api/pomodoros/today
Headers: Authorization: Bearer {jwt-token}

PUT /api/pomodoros/{id}
Headers: Authorization: Bearer {jwt-token}
Body: { "completed": true, "endTime": "2025-10-03T12:00:00Z" }

DELETE /api/pomodoros/{id}
Headers: Authorization: Bearer {jwt-token}
```

### Statistiques

```bash
GET /api/statistics
Headers: Authorization: Bearer {jwt-token}
```

### Paramètres

```bash
GET /api/settings
Headers: Authorization: Bearer {jwt-token}

PUT /api/settings
Headers: Authorization: Bearer {jwt-token}
Body: {
  "workDuration": 1500,
  "shortBreakDuration": 300,
  "longBreakDuration": 900,
  "pomodorosUntilLongBreak": 4
}
```

## 🏗️ Structure du Projet

```
.
├── backend/                    # Symfony Backend
│   ├── config/
│   │   ├── packages/
│   │   │   ├── security.yaml
│   │   │   ├── lexik_jwt_authentication.yaml
│   │   │   ├── nelmio_cors.yaml
│   │   │   └── knpu_oauth2_client.yaml
│   │   └── jwt/               # JWT keys (auto-generated)
│   ├── src/
│   │   ├── Controller/
│   │   │   ├── AuthController.php
│   │   │   ├── PomodoroController.php
│   │   │   ├── StatisticsController.php
│   │   │   └── SettingsController.php
│   │   ├── Entity/
│   │   │   ├── User.php
│   │   │   ├── PomodoroSession.php
│   │   │   └── UserSettings.php
│   │   └── Repository/
│   └── .env
│
├── frontend/                   # Vue.js 3 Frontend
│   ├── src/
│   │   ├── components/
│   │   │   └── pomodoro/
│   │   │       └── PomodoroTimer.vue
│   │   ├── views/
│   │   │   ├── LoginView.vue
│   │   │   ├── HomeView.vue
│   │   │   ├── StatisticsView.vue
│   │   │   └── SettingsView.vue
│   │   ├── stores/
│   │   │   ├── auth.js
│   │   │   └── pomodoro.js
│   │   ├── services/
│   │   │   ├── api.js
│   │   │   ├── auth.js
│   │   │   └── pomodoro.js
│   │   ├── router/
│   │   │   └── index.js
│   │   ├── App.vue
│   │   ├── main.js
│   │   └── style.css
│   ├── tailwind.config.js
│   ├── postcss.config.js
│   └── .env
│
└── docker-compose.yml
```

## 🔐 Sécurité

### JWT Configuration

- Les clés JWT sont générées automatiquement dans `backend/config/jwt/`
- Token TTL : 24 heures
- Toutes les routes `/api/*` (sauf login/register/auth/google) nécessitent un JWT valide

### CORS

- Configuré pour accepter les requêtes de `http://localhost:5173`
- Peut être modifié dans `backend/config/packages/nelmio_cors.yaml`

## 🎨 Personnalisation

### Couleurs Tailwind

Les couleurs personnalisées sont définies dans `frontend/tailwind.config.js` :

```javascript
colors: {
  'pomodoro-red': '#DC2626',    // Travail
  'pomodoro-green': '#16A34A',  // Pause longue
  'pomodoro-blue': '#2563EB',   // Pause courte
}
```

### Durées par défaut

Les durées par défaut sont définies dans l'entité `UserSettings.php` :

- Travail : 1500s (25 min)
- Pause courte : 300s (5 min)
- Pause longue : 900s (15 min)
- Pomodoros avant pause longue : 4

## 🐛 Dépannage

### Les containers ne démarrent pas

```bash
# Vérifier les logs
docker-compose logs -f

# Reconstruire les images
docker-compose down
docker-compose up --build
```

### Erreur CORS

Vérifiez que `CORS_ALLOW_ORIGIN` dans `backend/.env` correspond à l'URL du frontend.

### Erreur JWT

Régénérez les clés JWT :

```bash
docker exec -it pomodoro_backend bash
rm -rf config/jwt/*
openssl genpkey -out config/jwt/private.pem -algorithm RSA -pkeyopt rsa_keygen_bits:4096
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
exit
```

### Google Sign-In ne fonctionne pas

1. Vérifiez que le Client ID est correct dans `frontend/.env`
2. Vérifiez que `http://localhost:5173` est dans les origines autorisées
3. Vérifiez la console du navigateur pour les erreurs

### Base de données vide

```bash
docker exec -it pomodoro_backend php bin/console doctrine:schema:create
```

### Réinitialiser la base de données

```bash
docker exec -it pomodoro_backend bash
rm -f var/data.db
php bin/console doctrine:schema:create
exit
```

## 📝 Commandes utiles

```bash
# Arrêter tous les services
docker-compose down

# Voir les logs
docker-compose logs -f [service]

# Accéder au container backend
docker exec -it pomodoro_backend bash

# Accéder au container frontend
docker exec -it pomodoro_frontend sh

# Voir les entités et le schéma
docker exec -it pomodoro_backend php bin/console doctrine:schema:validate

# Mettre à jour le schéma
docker exec -it pomodoro_backend php bin/console doctrine:schema:update --force

# Réinitialiser complètement la base de données
docker exec -it pomodoro_backend bash -c "rm -f var/data.db && php bin/console doctrine:schema:create"
```

## 📦 Production

### Backend

1. Modifiez `APP_ENV=prod` dans `.env`
2. Pour la production, envisagez PostgreSQL/MySQL au lieu de SQLite
3. Générez de nouvelles clés JWT sécurisées
4. Configurez CORS pour votre domaine de production
5. Utilisez un serveur web (Nginx/Apache) avec PHP-FPM

### Frontend

```bash
# Build pour production
cd frontend
npm run build

# Les fichiers seront dans frontend/dist/
# Servez-les avec Nginx ou tout serveur statique
```

## 🤝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à ouvrir une issue ou une pull request.

## 📄 Licence

MIT

## 🎯 Roadmap

- [ ] PWA support (installable sur mobile)
- [ ] Dark mode
- [ ] Graphiques de statistiques (Chart.js)
- [ ] Export des données en CSV
- [ ] Refresh token automatique
- [ ] Statistiques hebdomadaires/mensuelles
- [ ] Sons personnalisables
- [ ] Thèmes personnalisables

## 👨‍💻 Auteur

Votre nom / email

---

**Bon travail avec la technique Pomodoro ! 🍅⏱️**

# Démarrage Rapide (Sans Docker)

Si vous rencontrez des problèmes avec Docker, voici comment démarrer l'application localement :

## Prérequis

- PHP 8.2+
- Composer
- PostgreSQL (ou utilisez SQLite pour un démarrage rapide)
- Node.js 20+
- npm

## Backend (Symfony)

```bash
cd backend

# Installer les dépendances
composer install

# Utiliser SQLite pour simplifier (optionnel)
# Modifier DATABASE_URL dans .env:
# DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"

# Créer la base de données
php bin/console doctrine:database:create --if-not-exists

# Créer le schéma
php bin/console doctrine:schema:create

# Démarrer le serveur
symfony server:start --port=8000
# OU si vous n'avez pas Symfony CLI:
php -S localhost:8000 -t public/
```

## Frontend (Vue.js)

```bash
cd frontend

# Installer les dépendances
npm install

# Démarrer le serveur de développement
npm run dev
```

## Configuration Google OAuth

1. Allez sur https://console.cloud.google.com/
2. Créez un projet
3. Activez Google+ API
4. Créez des credentials OAuth 2.0 Client ID
5. Ajoutez `http://localhost:5173` dans les origines autorisées
6. Copiez le Client ID et Secret

**Backend (.env)**:
```
GOOGLE_CLIENT_ID=votre-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=votre-client-secret
```

**Frontend (.env)**:
```
VITE_API_URL=http://localhost:8000
VITE_GOOGLE_CLIENT_ID=votre-client-id.apps.googleusercontent.com
```

## Accès

- Frontend: http://localhost:5173
- Backend API: http://localhost:8000/api

## Troubleshooting

### Erreur JWT
Si vous obtenez une erreur JWT, les clés sont déjà générées dans `backend/config/jwt/`

### Erreur CORS
Vérifiez que `CORS_ALLOW_ORIGIN` dans `backend/.env` permet `http://localhost:5173`

### Google Sign-In ne fonctionne pas
1. Vérifiez le Client ID dans `frontend/.env`
2. Vérifiez que `http://localhost:5173` est dans les origines autorisées
3. Videz le cache du navigateur

## Retour à Docker

Une fois que tout fonctionne localement, vous pouvez revenir à Docker :

```bash
# Reconstruire proprement
docker-compose down -v
docker-compose build --no-cache
docker-compose up -d

# Créer la BDD
docker exec -it pomodoro_backend php bin/console doctrine:database:create
docker exec -it pomodoro_backend php bin/console doctrine:schema:create
```

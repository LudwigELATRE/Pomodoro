# 🚀 Démarrage Rapide - Application Pomodoro

## ✅ Configuration SQLite - Prêt à l'emploi !

L'application utilise maintenant **SQLite** au lieu de PostgreSQL. Aucune configuration de base de données n'est nécessaire !

## 📦 Démarrage avec Docker (Recommandé)

### 1. Démarrer les containers

```bash
docker-compose up -d
```

### 2. La base de données est déjà créée !

Le fichier SQLite `backend/var/data.db` a été créé automatiquement (32KB).

### 3. Accéder à l'application

- **Frontend**: http://localhost:5173
- **Backend API**: http://localhost:8000/api

C'est tout ! 🎉

## ⚙️ Configuration Google OAuth (Optionnel)

Pour activer le "Sign in with Google" :

### 1. Créer un projet Google Cloud

1. Allez sur https://console.cloud.google.com/
2. Créez un nouveau projet
3. Activez Google+ API
4. Créez des credentials OAuth 2.0
5. Ajoutez `http://localhost:5173` dans les origines autorisées

### 2. Configurer les credentials

**backend/.env** :
```env
GOOGLE_CLIENT_ID=votre-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=votre-client-secret
```

**frontend/.env** :
```env
VITE_API_URL=http://localhost:8000
VITE_GOOGLE_CLIENT_ID=votre-client-id.apps.googleusercontent.com
```

### 3. Redémarrer les containers

```bash
docker-compose restart
```

## 🛠️ Commandes Utiles

### Voir les logs
```bash
docker-compose logs -f
```

### Arrêter l'application
```bash
docker-compose down
```

### Réinitialiser la base de données
```bash
docker exec pomodoro_backend bash -c "rm -f var/data.db && php bin/console doctrine:schema:create"
```

### Accéder au container backend
```bash
docker exec -it pomodoro_backend bash
```

## 📝 Utilisation Sans Docker

Si vous préférez exécuter localement sans Docker :

### Backend
```bash
cd backend
composer install
php bin/console doctrine:schema:create
php -S localhost:8000 -t public
```

### Frontend
```bash
cd frontend
npm install
npm run dev
```

## 🎯 Fonctionnalités

- ✅ Timer Pomodoro avec indicateur circulaire
- ✅ Authentification classique (email/password)
- ✅ Google Sign-In (après configuration)
- ✅ Notifications sonores et navigateur
- ✅ Statistiques détaillées
- ✅ Paramètres personnalisables
- ✅ Historique des sessions

## 🐛 Problèmes Courants

### Le frontend ne se connecte pas au backend

Vérifiez que `VITE_API_URL` dans `frontend/.env` pointe vers `http://localhost:8000`

### Erreur CORS

Le CORS est déjà configuré pour accepter `http://localhost:5173`. Si vous changez le port, modifiez `backend/config/packages/nelmio_cors.yaml`

### Les containers ne démarrent pas

```bash
# Nettoyer et reconstruire
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

## 📚 Documentation Complète

Consultez `README.md` pour la documentation détaillée incluant :
- Architecture complète
- Tous les endpoints API
- Configuration avancée
- Déploiement en production

---

**Profitez de la technique Pomodoro ! 🍅⏱️**

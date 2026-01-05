# 🧪 Étapes pour Tester l'Application

## ✅ Ce qui a été fait

1. ✅ Erreur PHPStan corrigée
2. ✅ OAuth2 Google configuré (reste à ajouter vos clés)
3. ✅ Messages traduits en français
4. ✅ Mise à jour du timer en cours lors du changement de settings
5. ✅ Assets compilés
6. ✅ Cache Symfony vidé
7. ✅ Documentation complète créée (ARCHITECTURE.md)

---

## 🚀 Étapes à Suivre MAINTENANT

### 1. Configurer Google OAuth (optionnel)

Si vous voulez l'authentification Google :

```bash
# Modifier .env et remplacer :
GOOGLE_CLIENT_ID=your-google-client-id        # ← Vos vraies clés
GOOGLE_CLIENT_SECRET=your-google-client-secret
```

Sinon, utilisez l'authentification email/password classique.

---

### 2. Démarrer le Serveur Symfony

```bash
symfony server:start
# OU
php -S localhost:8000 -t public/
```

Le serveur devrait être sur : **http://localhost:8000**

---

### 3. Démarrer le Serveur Frontend (si nécessaire)

Si vous utilisez Vite ou un autre serveur de dev :

```bash
npm run dev
```

Sinon, accédez directement à **http://localhost:8000**

---

### 4. Créer un Compte et Se Connecter

1. Allez sur **http://localhost:8000/login**
2. Créez un compte avec email/password
3. Connectez-vous

> ⚠️ **IMPORTANT** : Sans authentification, l'API retournera 401 et les settings ne se chargeront pas !

---

### 5. Tester le Flux Settings

#### A. Ouvrez la Console du Navigateur (F12 → Console)

#### B. Allez sur la page d'accueil (/)

Vous devriez voir :
```
[SettingsView] Chargement des paramètres...
Settings loaded from API: {workDuration: 1500, ...}
[SettingsView] Paramètres chargés depuis le store: {...}
```

✅ **Si vous voyez ça** → Les settings sont bien chargés depuis la BDD !

❌ **Si erreur 401** → Vous n'êtes pas connecté, retournez à l'étape 4

❌ **Si erreur 500** → Problème backend, vérifiez les logs Symfony

---

#### C. Allez dans Paramètres (/settings)

1. Changez la durée de travail (ex: de 25 à 30 minutes)
2. Cliquez sur "Enregistrer"

Vous devriez voir :
```
[SettingsView] Sauvegarde des paramètres: {workDuration: 1800, ...}
[SettingsView] Paramètres sauvegardés. Nouveaux paramètres du store: {...}
[SettingsView] Aucun minuteur en cours
```

Et le message en français : **"Paramètres enregistrés avec succès !"**

✅ **Si vous voyez ça** → Les settings sont bien sauvegardés en BDD !

---

#### D. Retournez sur la page d'accueil (/)

1. Cliquez sur "Démarrer"

Vous devriez voir :
```
[PomodoroStore] Démarrage d'un nouveau pomodoro
[PomodoroStore] Type de session: work
[PomodoroStore] Paramètres actuels: {workDuration: 1800, ...}
[PomodoroStore] Durée calculée: 1800 secondes
```

Et le timer devrait afficher **30:00** (au lieu de 25:00) !

✅ **Si le timer affiche 30:00** → TOUT FONCTIONNE ! 🎉

❌ **Si le timer affiche toujours 25:00** → Problème, envoyez-moi les logs de la console

---

### 6. Tester la Mise à Jour en Direct

1. Démarrez un timer (Démarrer)
2. Pendant que le timer tourne, allez dans Paramètres
3. Changez la durée de travail
4. Enregistrez

Vous devriez voir dans la console :
```
[SettingsView] Minuteur en cours détecté, mise à jour...
[SettingsView] Type: work, Nouvelle durée: 1800s
```

Et le timer devrait **immédiatement** passer à la nouvelle durée !

Message affiché : **"Paramètres enregistrés et minuteur actuel mis à jour !"**

---

## 🐛 Si Ça Ne Fonctionne Toujours Pas

### Problème : Message toujours en anglais

**Cause** : Cache du navigateur ou build incomplet

**Solution** :
```bash
# 1. Rebuild les assets
npm run build

# 2. Vider le cache Symfony
php bin/console cache:clear

# 3. Vider le cache du navigateur
# Chrome/Firefox : Ctrl+Shift+R (hard refresh)
# Ou ouvrir en navigation privée
```

---

### Problème : Erreur 401 Unauthorized

**Cause** : Pas de token JWT ou token expiré

**Solution** :
1. Déconnectez-vous
2. Reconnectez-vous
3. Le nouveau token sera stocké dans localStorage

---

### Problème : Settings ne se sauvegardent pas

**Test manuel de l'API** :

```bash
# 1. Récupérer un token (remplacez email/password)
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"password"}'

# Réponse : {"token": "eyJ0eXAi..."}

# 2. Tester GET settings
curl http://localhost:8000/api/settings \
  -H "Authorization: Bearer <VOTRE_TOKEN>"

# Devrait retourner : {"workDuration":1500,...}

# 3. Tester PUT settings
curl -X PUT http://localhost:8000/api/settings \
  -H "Authorization: Bearer <VOTRE_TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"workDuration":1800}'

# Devrait retourner : {"workDuration":1800,...}

# 4. Vérifier en BDD
php bin/console dbal:run-sql "SELECT * FROM user_settings"
```

---

## 📝 Logs à M'envoyer Si Problème

Ouvrez la console (F12) et copiez-moi TOUS les logs qui commencent par :
- `[SettingsView]`
- `[PomodoroStore]`
- `Settings loaded from API`

Ainsi que toutes les erreurs en rouge.

---

## 📚 Documentation Complète

Consultez **ARCHITECTURE.md** pour comprendre toute la structure de l'application.

---

## ✨ Résumé

```
1. Démarrer serveur Symfony (port 8000)
2. Créer un compte et se connecter
3. Ouvrir console navigateur (F12)
4. Tester le flux settings en suivant les logs
5. Si problème : m'envoyer les logs de la console
```

**Le problème DOIT être visible dans les logs de la console !** 🔍

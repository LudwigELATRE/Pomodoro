# 🔐 Debug Authentification

## Problème Identifié

L'API retourne du HTML au lieu de JSON car **vous n'êtes pas authentifié** !

```
Settings loaded from API: <!DOCTYPE html> ❌
// Au lieu de :
Settings loaded from API: {workDuration: 1500, ...} ✅
```

---

## ✅ Vérifier Votre Authentification

### 1. Ouvrez la Console du Navigateur

**F12 → Console → Tapez :**

```javascript
localStorage.getItem('token')
```

#### Résultat attendu :
```
"eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..." // Un long token JWT
```

#### Si vous obtenez `null` :
❌ **Vous n'êtes PAS connecté !**

---

### 2. Se Connecter

#### Option A : Créer un compte et se connecter

1. Allez sur **http://localhost:8000/login**
2. Si vous n'avez pas de compte, créez-en un
3. Connectez-vous avec email/password

#### Option B : Test rapide avec curl

```bash
# Créer un compte
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@test.com",
    "password": "password123",
    "name": "Test User"
  }'

# Se connecter
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@test.com",
    "password": "password123"
  }'

# Réponse :
{
  "token": "eyJ0eXAi...",
  "user": {...}
}
```

Copiez le token et stockez-le manuellement dans localStorage :

```javascript
// Dans la console du navigateur
localStorage.setItem('token', 'VOTRE_TOKEN_ICI')
```

---

### 3. Vérifier que le Token est Envoyé

Après vous être connecté, ouvrez :

**F12 → Network → Rechargez la page → Cliquez sur une requête "/api/settings"**

Dans les **Request Headers**, vous devez voir :

```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJ...
```

#### Si ce header n'apparaît pas :
❌ Le token n'est pas envoyé → Problème dans `assets/vue/services/api.js`

#### Si vous voyez "401 Unauthorized" :
❌ Token invalide ou expiré → Reconnectez-vous

#### Si vous voyez "200 OK" avec du JSON :
✅ **Tout fonctionne !**

---

## 🔧 Test Direct de l'API

Une fois connecté, testez directement l'API :

```bash
# Remplacez TOKEN par votre vrai token
TOKEN="eyJ0eXAi..."

# GET settings
curl http://localhost:8000/api/settings \
  -H "Authorization: Bearer $TOKEN"

# Devrait retourner :
{"workDuration":1500,"shortBreakDuration":300,"longBreakDuration":900,"pomodorosUntilLongBreak":4}

# PUT settings (changer workDuration à 10 minutes)
curl -X PUT http://localhost:8000/api/settings \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"workDuration":600}'

# Devrait retourner :
{"workDuration":600,"shortBreakDuration":300,"longBreakDuration":900,"pomodorosUntilLongBreak":4}
```

#### Si vous obtenez du HTML :
❌ Token invalide → Reconnectez-vous

#### Si vous obtenez du JSON :
✅ **L'API fonctionne !** Le problème est dans le frontend.

---

## 🐛 Problème Frontend Détecté

Regardez les logs :

```javascript
// Vous envoyez : workDuration: 300
[SettingsView] Sauvegarde des paramètres: {workDuration: 300, ...}

// Mais le store reçoit : workDuration: 1500 (anciennes valeurs!)
[SettingsView] Paramètres sauvegardés. Nouveaux paramètres du store: {workDuration: 1500, ...}
```

**Le store ne met pas à jour les settings correctement !**

Le problème est probablement dans `assets/vue/stores/pomodoro.js` dans la fonction `updateSettings`.

---

## ✅ Solution Immédiate

1. **Connectez-vous** sur http://localhost:8000/login
2. **Vérifiez** que le token est dans localStorage :
   ```javascript
   localStorage.getItem('token')
   ```
3. **Rechargez** la page
4. **Testez** de nouveau la sauvegarde des settings

Si le token est présent mais que l'API retourne toujours du HTML :
→ Le token est **expiré** → **Reconnectez-vous**

---

## 📝 Logs Attendus

Après connexion, vous devriez voir :

```javascript
// Au chargement
Settings loaded from API: {workDuration: 1500, shortBreakDuration: 300, ...} ✅

// À la sauvegarde
[SettingsView] Sauvegarde des paramètres: {workDuration: 300, ...}
[SettingsView] Paramètres sauvegardés. Nouveaux paramètres du store: {workDuration: 300, ...} ✅
                                                                      ^^^^^^^^^^^^^^^^^^^^
                                                                      Les MÊMES valeurs !
```

---

## 🚨 Si Ça Ne Fonctionne Toujours Pas

Envoyez-moi :

1. Le résultat de `localStorage.getItem('token')` (les 50 premiers caractères suffisent)
2. Les logs de la console lors de la sauvegarde
3. Le Network log de la requête PUT /api/settings (Status code + Response)

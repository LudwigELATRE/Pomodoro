# 🌐 Debug Network - Vérifier la Requête API

## Étapes à Suivre

### 1. Ouvrir les DevTools Network

1. **F12** → Onglet **"Network"** (ou "Réseau" en français)
2. **Cochez** "Preserve log" pour garder les logs
3. **Rechargez** la page

---

### 2. Tester la Sauvegarde

1. Allez dans **Paramètres** (/settings)
2. Changez **Durée de Travail** à **5 minutes** (300 secondes)
3. Cliquez sur **"Enregistrer"**

---

### 3. Analyser la Requête

Dans l'onglet Network, cherchez la ligne **"settings"** (ou filtrez par "settings")

#### A. Vérifier la Requête Envoyée (Request)

Cliquez sur la ligne **"settings"** → Onglet **"Payload"** (ou "Charge utile")

**Vous devriez voir :**
```json
{
  "workDuration": 300,
  "shortBreakDuration": 60,
  "longBreakDuration": 600,
  "pomodorosUntilLongBreak": 4
}
```

✅ Si vous voyez ça → La requête est bien envoyée avec les bonnes valeurs

---

#### B. Vérifier la Réponse (Response)

Toujours sur la ligne **"settings"** → Onglet **"Response"**

**Vous devriez voir :**
```json
{
  "workDuration": 300,
  "shortBreakDuration": 60,
  "longBreakDuration": 600,
  "pomodorosUntilLongBreak": 4
}
```

#### ❌ Si vous voyez les ANCIENNES valeurs (1500, 300, 900, 4) :
→ **Le backend NE sauvegarde PAS les nouvelles valeurs !**

#### ❌ Si vous voyez du HTML :
```html
<!DOCTYPE html>
<html>...
```
→ **La requête n'arrive PAS au bon endpoint**

#### ❌ Si vous voyez une erreur :
```json
{"error": "..."}
```
→ Copiez l'erreur et envoyez-la moi

---

#### C. Vérifier le Status Code

En haut de la fenêtre Network, regardez le **Status Code** :

- **200 OK** → La requête a réussi
- **401 Unauthorized** → Problème d'authentification
- **404 Not Found** → Route introuvable
- **500 Internal Server Error** → Erreur serveur

---

### 4. Screenshot à M'envoyer

Faites un **screenshot** de l'onglet Network montrant :

1. La ligne "settings" (méthode PUT)
2. Le Status Code (200, 401, 404, etc.)
3. L'onglet **"Response"** avec le JSON retourné

---

## 🚀 Démarrer le Serveur Symfony (si besoin)

Si vous n'avez pas de serveur qui tourne, démarrez-le :

```bash
# Option 1 : Symfony CLI (recommandé)
symfony server:start

# Option 2 : PHP Built-in Server
php -S localhost:8000 -t public/

# Option 3 : Serveur en arrière-plan
symfony server:start -d
# Voir les logs :
symfony server:log
```

Le serveur devrait démarrer sur **http://localhost:8000**

---

## 📋 Checklist

- [ ] Network tab ouvert
- [ ] Ligne "settings" visible après sauvegarde
- [ ] Request payload = nouvelles valeurs (300, 60, 600, 4)
- [ ] Response body = ??? (à vérifier)
- [ ] Status code = ??? (à vérifier)

---

## 🎯 Ce Que Je Cherche

**Réponse dans l'onglet Response :**

### Cas 1 : JSON avec les NOUVELLES valeurs
```json
{"workDuration": 300, ...}
```
→ ✅ Backend fonctionne ! Le problème est dans le frontend (store)

### Cas 2 : JSON avec les ANCIENNES valeurs
```json
{"workDuration": 1500, ...}
```
→ ❌ Backend NE sauvegarde PAS ! Problème Symfony/Doctrine

### Cas 3 : HTML
```html
<!DOCTYPE html>...
```
→ ❌ Route incorrecte ou redirection

---

**Envoyez-moi un screenshot de la Response !** 📸

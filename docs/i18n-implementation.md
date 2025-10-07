# Implémentation de l'Internationalisation (i18n)

**Date :** 2025-10-07
**Problème résolu :** Application monolingue → Application multilingue FR/EN

## Contexte

L'application Pomodoro était entièrement en anglais avec du texte hardcodé. Besoin d'ajouter le support du français tout en gardant l'anglais par défaut.

## Solution implémentée

### 1. Installation et Configuration de vue-i18n

**Package :** `vue-i18n@11.1.12`

**Fichier de config :** `frontend/src/i18n/index.js`
```javascript
import { createI18n } from 'vue-i18n'

const savedLocale = localStorage.getItem('language') || 'en'

const i18n = createI18n({
  legacy: false,
  locale: savedLocale,
  fallbackLocale: 'en',
  messages: { en, fr }
})
```

### 2. Structure des Fichiers de Traduction

**Fichiers :**
- `frontend/src/i18n/locales/en.json` (langue par défaut)
- `frontend/src/i18n/locales/fr.json`

**Structure JSON :**
```json
{
  "nav": { ... },
  "auth": { ... },
  "timer": { ... },
  "statistics": { ... },
  "settings": { ... },
  "common": { ... }
}
```

### 3. Composant LanguageSwitcher

**Fichier :** `frontend/src/components/LanguageSwitcher.vue`

Fonctionnalités :
- Dropdown avec drapeaux 🇬🇧 🇫🇷
- Sauvegarde dans localStorage
- Chargement automatique au démarrage
- Intégré dans Navbar et LoginView

### 4. Composant Navbar Réutilisable

**Fichier :** `frontend/src/components/Navbar.vue`

Composant créé pour éviter la répétition du code navbar dans toutes les vues. Inclut :
- Navigation (Timer, Statistics, Settings)
- Profil utilisateur avec avatar
- Sélecteur de langue
- Bouton Logout

### 5. Traductions dans les Composants

**Utilisation dans les templates :**
```vue
<h1>{{ $t('statistics.title') }}</h1>
```

**Utilisation dans le script :**
```javascript
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
const title = t('statistics.title')
```

## Impact

### Fichiers créés
- `frontend/src/i18n/index.js`
- `frontend/src/i18n/locales/en.json`
- `frontend/src/i18n/locales/fr.json`
- `frontend/src/components/LanguageSwitcher.vue`
- `frontend/src/components/Navbar.vue`

### Fichiers modifiés
- `frontend/src/main.js` : Ajout de `app.use(i18n)`
- Toutes les vues : Remplacement du texte hardcodé par `$t()`
  - `LoginView.vue`
  - `HomeView.vue`
  - `StatisticsView.vue`
  - `SettingsView.vue`
- Composant : `PomodoroTimer.vue`
- Store : Pas de modification (gestion côté composants)

### Comportement
- **Langue par défaut** : Anglais (EN)
- **Langues disponibles** : Anglais, Français
- **Persistance** : localStorage clé `language`
- **Fallback** : Si une clé manque en FR, utilise EN

## Notes techniques

### Pourquoi `legacy: false` ?

Le mode Composition API (`legacy: false`) offre :
- Meilleure performance
- Meilleure intégration avec Vue 3
- TypeScript support amélioré

### Chargement de la langue sauvegardée

```javascript
// Dans i18n/index.js
const savedLocale = localStorage.getItem('language') || 'en'

// Dans LanguageSwitcher.vue
const savedLanguage = localStorage.getItem('language')
if (savedLanguage) {
  locale.value = savedLanguage
}
```

Le chargement se fait à **deux endroits** :
1. `i18n/index.js` : Au démarrage de l'app (initialisation globale)
2. `LanguageSwitcher.vue` : Au montage du composant (synchronisation)

### Notifications du navigateur traduites

Les notifications système (timer terminé) sont également traduites :
```javascript
const title = t('timer.workSessionComplete')
new Notification(title, { body: t('timer.greatJobBreak') })
```

### Évolutions possibles
- Ajouter d'autres langues (ES, DE, IT...)
- Détection automatique de la langue du navigateur
- Traduction du backend (erreurs API)
- Pluralisation avancée avec vue-i18n
- Date/time formatting localisé
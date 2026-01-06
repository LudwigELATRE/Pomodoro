# Pomodoro Monolith

Application web de gestion du temps basée sur la technique Pomodoro, construite avec Symfony 7 et Vue.js 3.

![Pomodoro](public/pomodoro.png)

## Fonctionnalités

- **Timer Pomodoro** : Sessions de travail et pauses configurables
- **Authentification** :
  - Inscription/Connexion locale avec JWT
  - Connexion via Google OAuth2
- **Paramètres personnalisables** :
  - Durée des sessions de travail
  - Durée des pauses courtes et longues
  - Nombre de pomodoros avant pause longue
- **Statistiques** : Suivi et historique de vos sessions
- **Interface multilingue** : Support i18n avec Vue I18n
- **Design moderne** : Interface responsive avec Tailwind CSS

## Stack Technique

### Backend
- **Symfony 7.0** (PHP 8.2+)
- **Doctrine ORM** avec MySQL/MariaDB
- **Lexik JWT Authentication** pour l'authentification
- **KnpU OAuth2 Client** pour Google OAuth
- **CORS** configuré avec Nelmio CORS Bundle

### Frontend
- **Vue.js 3** avec Composition API
- **Pinia** pour la gestion d'état
- **Vue Router** pour le routing
- **Vue I18n** pour l'internationalisation
- **Axios** pour les requêtes HTTP
- **Tailwind CSS** pour le styling
- **Webpack Encore** pour le build

## Prérequis

- PHP >= 8.2
- Composer
- Node.js >= 18
- npm ou yarn
- MySQL/MariaDB >= 8.0
- Extension PHP : ctype, iconv

## Installation

### 1. Cloner le projet

```bash
git clone <repository-url>
cd pomodoro-monolith
```

### 2. Installation des dépendances

```bash
# Backend
composer install

# Frontend
npm install
```

### 3. Configuration

Copiez le fichier `.env` et configurez vos variables d'environnement :

```bash
cp .env .env.local
```

Modifiez `.env.local` avec vos paramètres :

```env
# Base de données
DATABASE_URL="mysql://username:password@127.0.0.1:3306/pomodoro?serverVersion=11.2.2-MariaDB&charset=utf8mb4"

# JWT (générer les clés avec la commande ci-dessous)
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=your-passphrase

# Google OAuth (optionnel)
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret

# CORS
CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'

# URL Frontend
FRONTEND_URL=http://localhost:8000
```

### 4. Générer les clés JWT

```bash
php bin/console lexik:jwt:generate-keypair
```

### 5. Créer la base de données

```bash
# Créer la base
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate
```

### 6. Build des assets

```bash
# Développement
npm run dev

# Développement avec watch
npm run watch

# Production
npm run build
```

## Démarrage

### Serveur de développement

```bash
# Backend (Symfony)
symfony server:start
# ou
php -S localhost:8000 -t public/

# Frontend (si dev-server)
npm run dev-server
```

L'application sera accessible sur `http://localhost:8000`

## Structure du Projet

```
pomodoro-monolith/
├── assets/                 # Fichiers Vue.js et CSS
│   ├── components/        # Composants Vue
│   ├── router/           # Configuration Vue Router
│   ├── stores/           # Stores Pinia
│   └── app.js            # Point d'entrée Vue
├── config/                # Configuration Symfony
│   ├── packages/         # Configuration des bundles
│   └── routes/           # Configuration des routes
├── migrations/            # Migrations de base de données
├── public/               # Point d'entrée web
│   └── build/           # Assets compilés
├── src/
│   ├── Controller/       # Contrôleurs Symfony
│   ├── Entity/          # Entités Doctrine
│   └── Repository/      # Repositories Doctrine
├── templates/            # Templates Twig
├── tests/               # Tests PHPUnit
├── .env                 # Variables d'environnement (template)
├── .env.local          # Variables d'environnement (local)
├── composer.json       # Dépendances PHP
├── package.json        # Dépendances Node.js
└── webpack.config.js   # Configuration Webpack Encore
```

## API Endpoints

### Authentification
- `POST /api/auth/register` - Inscription
- `POST /api/auth/login` - Connexion
- `GET /api/auth/google` - Redirection OAuth Google
- `GET /api/auth/google/callback` - Callback OAuth Google

### Pomodoro
- `GET /api/pomodoro/sessions` - Liste des sessions
- `POST /api/pomodoro/sessions` - Créer une session
- `PUT /api/pomodoro/sessions/{id}` - Mettre à jour une session
- `DELETE /api/pomodoro/sessions/{id}` - Supprimer une session

### Statistiques
- `GET /api/statistics` - Récupérer les statistiques

### Paramètres
- `GET /api/settings` - Récupérer les paramètres
- `PUT /api/settings` - Mettre à jour les paramètres

## Tests

```bash
# Exécuter les tests PHPUnit
php bin/phpunit

# Tests avec couverture
php bin/phpunit --coverage-html coverage/
```

## Déploiement

### Déploiement automatique sur O2switch

Le projet inclut un workflow GitHub Actions pour le déploiement automatique sur O2switch.

**🚀 Nouveau : Configuration simplifiée !**

```bash
# Générer automatiquement tous les secrets (JWT, APP_SECRET, etc.)
make setup-deployment
```

Cette commande génère un fichier `deployment-secrets.txt` avec toutes les valeurs à copier dans GitHub Secrets.

**Consultez le guide complet : [DEPLOYMENT.md](DEPLOYMENT.md)**

Le guide contient :
- **Script automatisé de génération des secrets** (recommandé !)
- Configuration de l'hébergement O2switch
- Configuration des secrets GitHub
- Structure des fichiers sur le serveur
- Configuration Apache (.htaccess)
- Troubleshooting et maintenance

### Déploiement manuel

```bash
# Vérifier que tout est prêt
make deploy-check

# Build de production
composer install --no-dev --optimize-autoloader
npm run build
APP_ENV=prod php bin/console cache:clear
APP_ENV=prod php bin/console cache:warmup

# Uploader les fichiers vers le serveur
# (via FTP ou rsync)
```

### Variables d'environnement en production

Assurez-vous de configurer les variables suivantes :

- `APP_ENV=prod`
- `APP_SECRET` (générer une clé sécurisée)
- `DATABASE_URL` (connexion à la base de données)
- `JWT_PASSPHRASE` (phrase secrète sécurisée)
- Configuration OAuth Google si utilisé

Voir [DEPLOYMENT.md](DEPLOYMENT.md) pour plus de détails.

## Commandes Utiles

```bash
# Créer une nouvelle entité
php bin/console make:entity

# Créer un contrôleur
php bin/console make:controller

# Générer une migration
php bin/console make:migration

# Vider le cache
php bin/console cache:clear

# Lister les routes
php bin/console debug:router

# Vérifier le schéma de la base
php bin/console doctrine:schema:validate
```

## Configuration OAuth Google

1. Créez un projet sur [Google Cloud Console](https://console.cloud.google.com/)
2. Activez l'API Google+
3. Créez des identifiants OAuth 2.0
4. Ajoutez les URLs de redirection autorisées :
   - `http://localhost:8000/api/auth/google/callback` (dev)
   - `https://your-domain.com/api/auth/google/callback` (prod)
5. Copiez le Client ID et Client Secret dans `.env.local`

## Sécurité

- Les mots de passe sont hashés avec l'algorithme bcrypt
- Les tokens JWT expirent après 1 heure (configurable)
- CORS configuré pour autoriser uniquement les origines spécifiées
- Protection CSRF activée sur les formulaires
- Validation des données côté serveur

## Contribution

1. Fork le projet
2. Créez une branche (`git checkout -b feature/amazing-feature`)
3. Committez vos changements (`git commit -m 'Add amazing feature'`)
4. Push vers la branche (`git push origin feature/amazing-feature`)
5. Ouvrez une Pull Request

## Troubleshooting

### Erreur de connexion à la base de données
Vérifiez que :
- MySQL/MariaDB est démarré
- Les credentials dans `DATABASE_URL` sont corrects
- La base de données existe

### Erreur JWT
Vérifiez que :
- Les clés JWT sont générées (`php bin/console lexik:jwt:generate-keypair`)
- Le dossier `config/jwt/` a les bonnes permissions
- La passphrase est correcte

### Assets non compilés
```bash
rm -rf public/build
npm run build
php bin/console cache:clear
```

## Licence

Ce projet est sous licence propriétaire.

## Auteur

Ludwig Elatre

## Support

Pour toute question ou problème, ouvrez une issue sur le dépôt GitHub.

# Quick Start Guide

Guide de démarrage rapide pour Pomodoro Monolith.

## Installation Rapide

```bash
# 1. Cloner le projet
git clone <repository-url>
cd pomodoro-monolith

# 2. Installer avec Makefile (recommandé)
make install-local

# Ou manuellement :
composer install
npm install
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console lexik:jwt:generate-keypair
```

## Configuration Minimale

Créez `.env.local` :

```env
APP_ENV=dev
DATABASE_URL="mysql://user:password@127.0.0.1:3306/pomodoro?serverVersion=11.2.2-MariaDB&charset=utf8mb4"
```

## Démarrage

```bash
# Méthode 1 : Makefile (tout en une commande)
make dev

# Méthode 2 : Commandes séparées
symfony server:start -d    # ou: php -S localhost:8000 -t public/
npm run watch              # dans un autre terminal
```

Accédez à : `http://localhost:8000`

## Commandes Essentielles

```bash
# Voir toutes les commandes disponibles
make help

# Build des assets
npm run build              # Production
npm run watch              # Développement avec watch
npm run dev                # Développement

# Base de données
make db-migrate            # Appliquer les migrations
make db-reset              # Réinitialiser la BDD
php bin/console doctrine:schema:validate  # Vérifier le schéma

# Cache
make cache-clear           # Vider le cache
php bin/console cache:clear --env=prod    # Cache production

# Tests
make test                  # Lancer les tests PHPUnit
```

## Structure Rapide

```
pomodoro-monolith/
├── assets/           # Vue.js (composants, stores, router)
├── config/           # Configuration Symfony
├── migrations/       # Migrations Doctrine
├── public/           # Point d'entrée web + assets compilés
├── src/
│   ├── Controller/   # API endpoints
│   ├── Entity/       # Modèles de données
│   └── Repository/   # Requêtes base de données
├── templates/        # Templates Twig
└── var/              # Cache et logs
```

## API Endpoints Principaux

```bash
# Auth
POST   /api/auth/register           # Inscription
POST   /api/auth/login              # Connexion
GET    /api/auth/google             # OAuth Google
GET    /api/auth/google/callback    # Callback Google

# Pomodoro
GET    /api/pomodoro/sessions       # Liste des sessions
POST   /api/pomodoro/sessions       # Créer une session
PUT    /api/pomodoro/sessions/{id}  # Mettre à jour
DELETE /api/pomodoro/sessions/{id}  # Supprimer

# Settings
GET    /api/settings                # Récupérer les paramètres
PUT    /api/settings                # Mettre à jour

# Statistics
GET    /api/statistics              # Statistiques utilisateur
```

## Déploiement

```bash
# 🚀 NOUVEAU : Générer automatiquement tous les secrets
make setup-deployment

# Vérifier avant déploiement
make deploy-check

# Build production
npm run build
composer install --no-dev --optimize-autoloader
```

**`make setup-deployment`** génère automatiquement :
- APP_SECRET
- JWT_PASSPHRASE et clés JWT
- Fichier `deployment-secrets.txt` prêt pour GitHub Secrets

Voir [DEPLOYMENT.md](DEPLOYMENT.md) pour le déploiement complet sur O2switch.

## Troubleshooting Rapide

### Erreur "Access denied for user"
```bash
# Vérifier DATABASE_URL dans .env.local
# Créer la base : make db-create
```

### Erreur "Failed to load resource: /build/app.js"
```bash
npm run build    # Compiler les assets
```

### Erreur JWT
```bash
make jwt-generate    # Régénérer les clés
```

### Cache bloqué
```bash
rm -rf var/cache/*
make cache-clear
```

## Ressources

- [README.md](README.md) - Documentation complète
- [DEPLOYMENT.md](DEPLOYMENT.md) - Guide de déploiement O2switch
- [Makefile](Makefile) - Liste des commandes disponibles
- [Symfony Docs](https://symfony.com/doc/current/index.html)
- [Vue.js Docs](https://vuejs.org/guide/introduction.html)

## Support

Besoin d'aide ?
- Ouvrez une issue sur GitHub
- Consultez la documentation complète dans README.md
- Vérifiez les logs : `tail -f var/log/dev.log`

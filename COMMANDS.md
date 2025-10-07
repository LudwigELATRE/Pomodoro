# 📝 Commandes Essentielles

## 🚀 Démarrage Rapide

```bash
# Démarrer l'application
docker-compose up -d

# Vérifier le statut
docker-compose ps

# Accéder à l'application
open http://localhost:5173
```

## 🛑 Arrêt et Nettoyage

```bash
# Arrêter les containers
docker-compose down

# Arrêter et supprimer les volumes
docker-compose down -v

# Nettoyer complètement
docker-compose down --rmi all -v
```

## 🔍 Logs et Debugging

```bash
# Voir tous les logs en temps réel
docker-compose logs -f

# Logs du backend uniquement
docker-compose logs -f backend

# Logs du frontend uniquement
docker-compose logs -f frontend

# Dernières 50 lignes
docker logs pomodoro_backend --tail 50
```

## 💾 Base de Données

```bash
# Vérifier que la BDD existe
docker exec pomodoro_backend ls -lh var/data.db

# Voir le schéma
docker exec pomodoro_backend php bin/console doctrine:schema:validate

# Mettre à jour le schéma
docker exec pomodoro_backend php bin/console doctrine:schema:update --force

# Réinitialiser la base de données
docker exec pomodoro_backend bash -c "rm -f var/data.db && php bin/console doctrine:schema:create"

# Vider toutes les données (garder la structure)
docker exec pomodoro_backend bash -c "rm -f var/data.db && php bin/console doctrine:schema:create"
```

## 🔧 Accès aux Containers

```bash
# Accéder au container backend
docker exec -it pomodoro_backend bash

# Accéder au container frontend
docker exec -it pomodoro_frontend sh

# Exécuter une commande Symfony
docker exec pomodoro_backend php bin/console <commande>

# Exécuter une commande npm
docker exec pomodoro_frontend npm <commande>
```

## 📦 Gestion des Dépendances

### Backend (Composer)
```bash
# Installer une nouvelle dépendance
docker exec pomodoro_backend composer require <package>

# Mettre à jour les dépendances
docker exec pomodoro_backend composer update

# Voir les dépendances
docker exec pomodoro_backend composer show
```

### Frontend (npm)
```bash
# Installer une nouvelle dépendance
docker exec pomodoro_frontend npm install <package>

# Mettre à jour les dépendances
docker exec pomodoro_frontend npm update

# Voir les dépendances
docker exec pomodoro_frontend npm list
```

## 🔄 Rebuild et Restart

```bash
# Reconstruire les images
docker-compose build

# Reconstruire sans cache
docker-compose build --no-cache

# Rebuild et redémarrer
docker-compose up -d --build

# Redémarrer un service
docker-compose restart backend
docker-compose restart frontend

# Redémarrer tous les services
docker-compose restart
```

## 🧪 Tests et Validation

```bash
# Valider la configuration Symfony
docker exec pomodoro_backend php bin/console debug:config

# Voir les routes
docker exec pomodoro_backend php bin/console debug:router

# Lister les services
docker exec pomodoro_backend php bin/console debug:container

# Tester la connexion à la BDD
docker exec pomodoro_backend php bin/console dbal:run-sql "SELECT 1"

# Build frontend pour production
docker exec pomodoro_frontend npm run build
```

## 🔐 JWT Keys

```bash
# Régénérer les clés JWT
docker exec pomodoro_backend bash -c "rm -rf config/jwt/* && \
  openssl genpkey -out config/jwt/private.pem -algorithm RSA -pkeyopt rsa_keygen_bits:4096 && \
  openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem"

# Vérifier que les clés existent
docker exec pomodoro_backend ls -lh config/jwt/
```

## 📊 Monitoring

```bash
# Voir l'utilisation des ressources
docker stats

# Voir l'espace disque utilisé
docker system df

# Inspecter un container
docker inspect pomodoro_backend

# Voir les processus dans un container
docker top pomodoro_backend
```

## 🌐 Réseau

```bash
# Voir les réseaux Docker
docker network ls

# Inspecter le réseau de l'application
docker network inspect vuejs_pomodoro_network

# Tester la connectivité entre containers
docker exec pomodoro_frontend ping -c 3 pomodoro_backend
```

## 📝 Fichiers et Permissions

```bash
# Voir les fichiers du backend
docker exec pomodoro_backend ls -la

# Voir les permissions du fichier SQLite
docker exec pomodoro_backend ls -lh var/data.db

# Changer les permissions si nécessaire
docker exec pomodoro_backend chmod 666 var/data.db

# Voir l'espace utilisé par le projet
du -sh backend frontend
```

## 🔄 Mise à Jour du Projet

```bash
# Mettre à jour le code et redémarrer
git pull
docker-compose down
docker-compose up -d --build

# Réinstaller les dépendances
docker exec pomodoro_backend composer install
docker exec pomodoro_frontend npm install
```

## 🆘 Résolution de Problèmes

```bash
# Container ne démarre pas
docker-compose logs backend
docker-compose logs frontend

# Erreur de port déjà utilisé
lsof -i :8000  # Voir qui utilise le port 8000
lsof -i :5173  # Voir qui utilise le port 5173

# Nettoyer Docker complètement
docker system prune -a

# Supprimer les images non utilisées
docker image prune -a

# Supprimer les volumes non utilisés
docker volume prune
```

## 🎯 Commandes Utiles en Développement

```bash
# Voir les changements en temps réel (backend)
docker-compose logs -f backend

# Voir les changements en temps réel (frontend)
docker-compose logs -f frontend

# Effacer le cache Symfony
docker exec pomodoro_backend php bin/console cache:clear

# Tester l'API avec curl
curl http://localhost:8000/api/me

# Vérifier que le frontend est accessible
curl http://localhost:5173
```

## 📱 Accès depuis d'autres appareils

```bash
# Trouver votre IP locale
ipconfig getifaddr en0  # macOS
ip addr show           # Linux

# Accéder depuis un autre appareil
# Frontend: http://<votre-ip>:5173
# Backend: http://<votre-ip>:8000
```

---

## 💡 Commandes les Plus Utilisées

```bash
# Démarrer
docker-compose up -d

# Arrêter
docker-compose down

# Logs
docker-compose logs -f

# Redémarrer après changement
docker-compose restart

# Réinitialiser la BDD
docker exec pomodoro_backend bash -c "rm -f var/data.db && php bin/console doctrine:schema:create"
```

---

**Gardez ce fichier sous la main pour référence rapide ! 📌**

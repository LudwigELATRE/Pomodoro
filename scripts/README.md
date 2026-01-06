# Scripts de Déploiement

Ce dossier contient les scripts d'automatisation pour le déploiement.

## setup-deployment.sh

**Script principal pour configurer le déploiement sur O2switch.**

### Utilisation

```bash
# Via Makefile (recommandé)
make setup-deployment

# Directement
bash scripts/setup-deployment.sh

# Depuis le dossier scripts
cd scripts && ./setup-deployment.sh
```

### Ce que fait le script

1. **Génère APP_SECRET** (32 caractères hexadécimaux)
   - Utilisé par Symfony pour sécuriser les sessions et autres fonctionnalités

2. **Génère JWT_PASSPHRASE** (32 caractères alphanumériques)
   - Phrase secrète pour chiffrer les clés JWT

3. **Génère les clés JWT** (RSA 4096 bits)
   - Clé privée (chiffrée avec le passphrase)
   - Clé publique
   - Copie automatique dans `config/jwt/`

4. **Crée les fichiers de sortie** :
   - `deployment-secrets.txt` : Fichier formaté avec toutes les valeurs
   - `.github-secrets.env` : Fichier de référence pour le développement local

### Fichiers générés

```
├── deployment-secrets.txt       # À copier dans GitHub Secrets
├── .github-secrets.env          # Référence locale (ignoré par Git)
└── config/jwt/
    ├── private.pem              # Clé privée JWT (ignorée par Git)
    └── public.pem               # Clé publique JWT (ignorée par Git)
```

### Sécurité

⚠️ **IMPORTANT** : Les fichiers générés contiennent des informations sensibles !

- ✅ Automatiquement ignorés par Git (via `.gitignore`)
- ⚠️ Supprimez `deployment-secrets.txt` après configuration
- 🔒 Conservez une copie sécurisée dans un gestionnaire de mots de passe
- ❌ Ne partagez JAMAIS ces fichiers publiquement

### Prérequis

- PHP >= 7.0 (pour la génération via Symfony)
- OpenSSL (généralement préinstallé sur macOS/Linux)

### Workflow de déploiement

```bash
# 1. Générer les secrets
make setup-deployment

# 2. Ouvrir le fichier généré
cat deployment-secrets.txt

# 3. Copier les valeurs dans GitHub
# Settings > Secrets and variables > Actions > New repository secret

# 4. Ajouter les informations O2switch
# FTP_SERVER, SSH_HOST, DATABASE_URL, etc.

# 5. Tester le déploiement
# GitHub > Actions > Deploy to O2switch > Run workflow

# 6. Nettoyer
rm deployment-secrets.txt
```

### Régénération

Si vous devez régénérer les secrets :

```bash
# Sauvegarder les anciennes clés (si nécessaire)
cp config/jwt/private.pem config/jwt/private.pem.backup
cp config/jwt/public.pem config/jwt/public.pem.backup

# Régénérer
make setup-deployment

# Les anciennes clés seront écrasées
```

⚠️ **Attention** : Régénérer les clés JWT invalidera tous les tokens existants !

### Dépannage

#### Erreur : "PHP n'est pas installé"
```bash
# Installer PHP (macOS avec Homebrew)
brew install php

# Vérifier l'installation
php --version
```

#### Erreur : "OpenSSL n'est pas installé"
```bash
# OpenSSL devrait être préinstallé sur macOS/Linux
# Vérifier
openssl version

# Si manquant (macOS)
brew install openssl
```

#### Permission denied
```bash
# Rendre le script exécutable
chmod +x scripts/setup-deployment.sh
```

#### Les clés ne sont pas copiées dans config/jwt/
```bash
# Créer le dossier manuellement
mkdir -p config/jwt

# Relancer le script
make setup-deployment
```

## Ajouter d'autres scripts

Pour ajouter de nouveaux scripts d'automatisation :

1. Créez le script dans `scripts/`
2. Rendez-le exécutable : `chmod +x scripts/your-script.sh`
3. Ajoutez une commande dans le `Makefile`
4. Documentez-le dans ce README

### Template de script

```bash
#!/bin/bash

set -e

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}Mon script${NC}"

# Votre code ici

echo -e "${GREEN}✅ Terminé !${NC}"
```

## Ressources

- [DEPLOYMENT.md](../DEPLOYMENT.md) : Guide complet de déploiement
- [QUICK_START.md](../QUICK_START.md) : Guide de démarrage rapide
- [Makefile](../Makefile) : Liste des commandes disponibles

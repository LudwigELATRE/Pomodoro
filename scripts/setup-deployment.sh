#!/bin/bash

# Script d'automatisation de la configuration du déploiement
# Génère toutes les clés et secrets nécessaires pour le déploiement sur O2switch

set -e

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color
BOLD='\033[1m'

echo -e "${BLUE}${BOLD}"
echo "╔════════════════════════════════════════════════════════════╗"
echo "║   Configuration du Déploiement - Pomodoro Monolith        ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo -e "${NC}"

# Fichier de sortie
OUTPUT_FILE="deployment-secrets.txt"
SECRETS_FILE=".github-secrets.env"

# Vérifier si PHP est disponible
if ! command -v php &> /dev/null; then
    echo -e "${RED}❌ PHP n'est pas installé ou n'est pas dans le PATH${NC}"
    exit 1
fi

# Vérifier si openssl est disponible
if ! command -v openssl &> /dev/null; then
    echo -e "${RED}❌ OpenSSL n'est pas installé ou n'est pas dans le PATH${NC}"
    exit 1
fi

echo -e "${YELLOW}📋 Génération des secrets de déploiement...${NC}\n"

# 1. Générer APP_SECRET
echo -e "${GREEN}[1/4]${NC} Génération de APP_SECRET..."
APP_SECRET=$(openssl rand -hex 32)

# 2. Générer JWT_PASSPHRASE
echo -e "${GREEN}[2/4]${NC} Génération de JWT_PASSPHRASE..."
JWT_PASSPHRASE=$(openssl rand -base64 32 | tr -d "=+/" | cut -c1-32)

# 3. Générer les clés JWT
echo -e "${GREEN}[3/4]${NC} Génération des clés JWT..."

# Créer un dossier temporaire pour les clés
TEMP_JWT_DIR=$(mktemp -d)
mkdir -p "$TEMP_JWT_DIR"

# Générer les clés JWT avec le passphrase
openssl genpkey -out "$TEMP_JWT_DIR/private.pem" -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 -pass pass:"$JWT_PASSPHRASE" 2>/dev/null
openssl pkey -in "$TEMP_JWT_DIR/private.pem" -passin pass:"$JWT_PASSPHRASE" -pubout -out "$TEMP_JWT_DIR/public.pem" 2>/dev/null

# Lire le contenu des clés
JWT_PRIVATE_KEY=$(cat "$TEMP_JWT_DIR/private.pem")
JWT_PUBLIC_KEY=$(cat "$TEMP_JWT_DIR/public.pem")

# Copier aussi dans le projet (pour le développement local)
mkdir -p config/jwt
cp "$TEMP_JWT_DIR/private.pem" config/jwt/private.pem
cp "$TEMP_JWT_DIR/public.pem" config/jwt/public.pem
chmod 600 config/jwt/private.pem
chmod 644 config/jwt/public.pem

echo -e "${GREEN}✅ Clés JWT copiées dans config/jwt/${NC}"

# 4. Créer le fichier de sortie
echo -e "${GREEN}[4/4]${NC} Création du fichier de configuration..."

cat > "$OUTPUT_FILE" << EOF
╔════════════════════════════════════════════════════════════════════════════╗
║                    SECRETS GITHUB POUR LE DÉPLOIEMENT                      ║
║                          Pomodoro Monolith                                 ║
╚════════════════════════════════════════════════════════════════════════════╝

📅 Généré le : $(date '+%Y-%m-%d %H:%M:%S')

⚠️  IMPORTANT : Ce fichier contient des informations sensibles !
    - Ne le commitez JAMAIS dans Git
    - Supprimez-le après avoir configuré GitHub Secrets
    - Conservez une copie sécurisée en lieu sûr

═══════════════════════════════════════════════════════════════════════════

SECRETS GÉNÉRÉS AUTOMATIQUEMENT
═══════════════════════════════════════════════════════════════════════════

1. APP_SECRET
─────────────
$APP_SECRET

2. JWT_PASSPHRASE
─────────────────
$JWT_PASSPHRASE

3. JWT_PRIVATE_KEY
──────────────────
$JWT_PRIVATE_KEY

4. JWT_PUBLIC_KEY
─────────────────
$JWT_PUBLIC_KEY

═══════════════════════════════════════════════════════════════════════════

SECRETS À CONFIGURER MANUELLEMENT
═══════════════════════════════════════════════════════════════════════════

Connexion FTP/SSH O2switch :
─────────────────────────────
FTP_SERVER=ftp.votre-domaine.com
FTP_USERNAME=votre-username
FTP_PASSWORD=votre-mot-de-passe
FTP_SERVER_DIR=/www/pomodoro/

SSH_HOST=votre-domaine.com
SSH_USERNAME=votre-username
SSH_PASSWORD=votre-mot-de-passe
SSH_PORT=22
REMOTE_PATH=/home/username/www/pomodoro

Base de données :
─────────────────
DATABASE_URL=mysql://user:password@127.0.0.1:3306/pomodoro?serverVersion=11.2.2-MariaDB&charset=utf8mb4

CORS et Frontend :
──────────────────
CORS_ALLOW_ORIGIN=^https?://(votre-domaine\.com)(:[0-9]+)?$
FRONTEND_URL=https://votre-domaine.com

Google OAuth (optionnel) :
──────────────────────────
GOOGLE_CLIENT_ID=votre-client-id
GOOGLE_CLIENT_SECRET=votre-client-secret

Mailer (optionnel) :
────────────────────
MAILER_DSN=smtp://user:pass@smtp.example.com:587

═══════════════════════════════════════════════════════════════════════════

INSTRUCTIONS
═══════════════════════════════════════════════════════════════════════════

1. Allez sur GitHub : https://github.com/LudwigELATRE/Pomodoro/settings/secrets/actions

2. Pour chaque secret ci-dessus :
   - Cliquez sur "New repository secret"
   - Nom : Le nom du secret (ex: APP_SECRET)
   - Value : La valeur correspondante
   - Cliquez sur "Add secret"

3. Remplissez les valeurs manuelles avec vos informations O2switch

4. Une fois tous les secrets configurés, testez le déploiement :
   - Allez dans l'onglet "Actions"
   - Cliquez sur "Deploy to O2switch"
   - Cliquez sur "Run workflow"

═══════════════════════════════════════════════════════════════════════════
EOF

# Créer aussi un fichier .env pour référence (sera dans .gitignore)
cat > "$SECRETS_FILE" << EOF
# Fichier de référence pour les secrets GitHub
# Ce fichier est automatiquement ignoré par Git
# NE PAS COMMITTER CE FICHIER

# Secrets générés automatiquement
APP_SECRET=$APP_SECRET
JWT_PASSPHRASE=$JWT_PASSPHRASE

# Les clés JWT sont dans config/jwt/
# JWT_PRIVATE_KEY=voir config/jwt/private.pem
# JWT_PUBLIC_KEY=voir config/jwt/public.pem

# Secrets à configurer manuellement
# FTP_SERVER=ftp.votre-domaine.com
# FTP_USERNAME=votre-username
# FTP_PASSWORD=votre-mot-de-passe
# FTP_SERVER_DIR=/www/pomodoro/
# SSH_HOST=votre-domaine.com
# SSH_USERNAME=votre-username
# SSH_PASSWORD=votre-mot-de-passe
# SSH_PORT=22
# REMOTE_PATH=/home/username/www/pomodoro
# DATABASE_URL=mysql://user:password@127.0.0.1:3306/pomodoro?serverVersion=11.2.2-MariaDB&charset=utf8mb4
# CORS_ALLOW_ORIGIN=^https?://(votre-domaine\.com)(:[0-9]+)?$
# FRONTEND_URL=https://votre-domaine.com
# GOOGLE_CLIENT_ID=
# GOOGLE_CLIENT_SECRET=
# MAILER_DSN=
EOF

# Mettre à jour .gitignore pour ignorer ces fichiers
if ! grep -q "deployment-secrets.txt" .gitignore 2>/dev/null; then
    cat >> .gitignore << EOF

# Deployment secrets (never commit!)
deployment-secrets.txt
.github-secrets.env
scripts/deployment-secrets.txt
EOF
fi

# Nettoyer le dossier temporaire
rm -rf "$TEMP_JWT_DIR"

echo ""
echo -e "${GREEN}${BOLD}✅ Configuration terminée avec succès !${NC}\n"
echo -e "${YELLOW}📄 Fichiers créés :${NC}"
echo -e "   ${BLUE}→${NC} $OUTPUT_FILE ${GREEN}(tous les secrets)${NC}"
echo -e "   ${BLUE}→${NC} $SECRETS_FILE ${GREEN}(référence locale)${NC}"
echo -e "   ${BLUE}→${NC} config/jwt/private.pem ${GREEN}(clé privée JWT)${NC}"
echo -e "   ${BLUE}→${NC} config/jwt/public.pem ${GREEN}(clé publique JWT)${NC}"
echo ""
echo -e "${YELLOW}📋 Prochaines étapes :${NC}"
echo -e "   ${BLUE}1.${NC} Ouvrez le fichier : ${BOLD}$OUTPUT_FILE${NC}"
echo -e "   ${BLUE}2.${NC} Copiez chaque secret dans GitHub Settings > Secrets and variables > Actions"
echo -e "   ${BLUE}3.${NC} Remplissez les informations O2switch (FTP, SSH, Database)"
echo -e "   ${BLUE}4.${NC} Testez le déploiement depuis GitHub Actions"
echo ""
echo -e "${RED}⚠️  SÉCURITÉ :${NC}"
echo -e "   ${RED}→${NC} Supprimez le fichier ${BOLD}$OUTPUT_FILE${NC} après configuration"
echo -e "   ${RED}→${NC} Ne commitez JAMAIS ces fichiers dans Git"
echo -e "   ${RED}→${NC} Conservez une copie sécurisée dans un gestionnaire de mots de passe"
echo ""
echo -e "${GREEN}🚀 Bon déploiement !${NC}\n"

# Afficher le contenu du fichier
if command -v cat &> /dev/null; then
    echo -e "${BLUE}${BOLD}Aperçu du fichier généré :${NC}\n"
    cat "$OUTPUT_FILE"
fi

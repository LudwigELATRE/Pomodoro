# GitHub Secrets Template

Ce fichier contient la liste complète des secrets à configurer dans GitHub pour le déploiement automatique.

## Comment configurer les secrets

1. Allez sur votre dépôt GitHub
2. Cliquez sur `Settings` > `Secrets and variables` > `Actions`
3. Cliquez sur `New repository secret`
4. Ajoutez chaque secret avec son nom et sa valeur

## Liste des secrets requis

### Connexion FTP

```
Nom: FTP_SERVER
Valeur: ftp.votre-domaine.com
Description: Adresse du serveur FTP O2switch
```

```
Nom: FTP_USERNAME
Valeur: votre-username
Description: Nom d'utilisateur FTP
```

```
Nom: FTP_PASSWORD
Valeur: votre-mot-de-passe
Description: Mot de passe FTP
```

```
Nom: FTP_SERVER_DIR
Valeur: /www/pomodoro/
Description: Chemin du dossier sur le serveur FTP (se termine par /)
```

### Connexion SSH

```
Nom: SSH_HOST
Valeur: votre-domaine.com
Description: Adresse du serveur SSH
```

```
Nom: SSH_USERNAME
Valeur: votre-username
Description: Nom d'utilisateur SSH (généralement le même que FTP)
```

```
Nom: SSH_PASSWORD
Valeur: votre-mot-de-passe
Description: Mot de passe SSH (généralement le même que FTP)
```

```
Nom: SSH_PORT
Valeur: 22
Description: Port SSH (généralement 22)
```

```
Nom: REMOTE_PATH
Valeur: /home/username/www/pomodoro
Description: Chemin absolu vers le dossier du projet sur le serveur
```

### Configuration Symfony

```
Nom: APP_SECRET
Valeur: [générer avec: openssl rand -hex 32]
Description: Clé secrète Symfony pour la sécurité
Exemple: 5f8c7d9e2b4a1f6c3d8e9a7b5c4d2e1f3a6b9c8d7e5f4a3b2c1d9e8f7a6b5c4d
```

```
Nom: DATABASE_URL
Valeur: mysql://user:password@127.0.0.1:3306/database?serverVersion=10.11.2-MariaDB&charset=utf8mb4
Description: URL de connexion à la base de données MySQL
Exemple: mysql://cpanel_pomodoro:P@ssw0rd!@127.0.0.1:3306/cpanel_pomodoro_db?serverVersion=10.11.2-MariaDB&charset=utf8mb4
```

### JWT Authentication

```
Nom: JWT_PASSPHRASE
Valeur: [votre passphrase sécurisée]
Description: Phrase secrète pour les clés JWT (utilisée lors de la génération)
Exemple: super_secret_passphrase_2024!
```

```
Nom: JWT_PRIVATE_KEY
Valeur: [contenu complet de config/jwt/private.pem]
Description: Clé privée JWT (inclure les lignes BEGIN/END)
Format:
-----BEGIN RSA PRIVATE KEY-----
MIIJKQIBAAKCAgEA...
[contenu de la clé]
...
-----END RSA PRIVATE KEY-----
```

```
Nom: JWT_PUBLIC_KEY
Valeur: [contenu complet de config/jwt/public.pem]
Description: Clé publique JWT (inclure les lignes BEGIN/END)
Format:
-----BEGIN PUBLIC KEY-----
MIICIjANBgkqhkiG9w0BA...
[contenu de la clé]
...
-----END PUBLIC KEY-----
```

### CORS et Frontend

```
Nom: CORS_ALLOW_ORIGIN
Valeur: ^https?://(votre-domaine\.com)(:[0-9]+)?$
Description: Expression régulière pour les origines CORS autorisées
Exemple: ^https?://(pomodoro\.example\.com|example\.com)(:[0-9]+)?$
```

```
Nom: FRONTEND_URL
Valeur: https://votre-domaine.com
Description: URL complète du frontend
Exemple: https://pomodoro.example.com
```

### Google OAuth (optionnel)

```
Nom: GOOGLE_CLIENT_ID
Valeur: [votre Google Client ID]
Description: Client ID de l'application Google OAuth
Exemple: 123456789-abcdefgh.apps.googleusercontent.com
```

```
Nom: GOOGLE_CLIENT_SECRET
Valeur: [votre Google Client Secret]
Description: Client Secret de l'application Google OAuth
Exemple: GOCSPX-abcdefghijklmnop
```

### Mailer (optionnel)

```
Nom: MAILER_DSN
Valeur: smtp://user:pass@smtp.example.com:587
Description: Configuration du serveur mail
Exemples:
- Gmail: gmail://username:password@default
- SMTP: smtp://user:pass@smtp.example.com:587
- Null (dev): null://null
```

## Génération des valeurs

### APP_SECRET

Générez une clé secrète de 32 caractères hexadécimaux :

```bash
openssl rand -hex 32
```

### JWT_PASSPHRASE

Créez une phrase secrète sécurisée (au moins 16 caractères) :

```bash
openssl rand -base64 32
```

### Clés JWT (JWT_PRIVATE_KEY et JWT_PUBLIC_KEY)

Générez les clés sur votre serveur O2switch via SSH :

```bash
# Se connecter au serveur
ssh username@votre-domaine.com

# Aller dans le dossier du projet
cd ~/www/pomodoro

# Générer les clés
php bin/console lexik:jwt:generate-keypair

# Afficher la clé privée (à copier dans JWT_PRIVATE_KEY)
cat config/jwt/private.pem

# Afficher la clé publique (à copier dans JWT_PUBLIC_KEY)
cat config/jwt/public.pem
```

**Important** : Copiez le contenu COMPLET des fichiers, y compris les lignes `-----BEGIN` et `-----END`.

### DATABASE_URL

Format : `mysql://username:password@host:port/database?serverVersion=X&charset=utf8mb4`

Remplacez :
- `username` : nom d'utilisateur MySQL (format : `cpanel_user_dbname`)
- `password` : mot de passe de l'utilisateur MySQL
- `host` : généralement `127.0.0.1` sur O2switch
- `port` : généralement `3306`
- `database` : nom de la base de données (format : `cpanel_user_dbname`)
- `serverVersion` : version de MariaDB (vérifiez dans cPanel phpMyAdmin)

## Vérification

Après avoir configuré tous les secrets, vous pouvez vérifier la configuration :

1. Dans votre dépôt GitHub, allez dans `Actions`
2. Cliquez sur le workflow `Deploy to O2switch`
3. Cliquez sur `Run workflow` > `Run workflow`
4. Suivez l'exécution pour vérifier qu'il n'y a pas d'erreurs

## Checklist

Cochez chaque secret après l'avoir configuré :

- [ ] FTP_SERVER
- [ ] FTP_USERNAME
- [ ] FTP_PASSWORD
- [ ] FTP_SERVER_DIR
- [ ] SSH_HOST
- [ ] SSH_USERNAME
- [ ] SSH_PASSWORD
- [ ] SSH_PORT
- [ ] REMOTE_PATH
- [ ] APP_SECRET
- [ ] DATABASE_URL
- [ ] JWT_PASSPHRASE
- [ ] JWT_PRIVATE_KEY
- [ ] JWT_PUBLIC_KEY
- [ ] CORS_ALLOW_ORIGIN
- [ ] FRONTEND_URL
- [ ] GOOGLE_CLIENT_ID (si utilisé)
- [ ] GOOGLE_CLIENT_SECRET (si utilisé)
- [ ] MAILER_DSN (si utilisé)

## Sécurité

**IMPORTANT** :
- Ne commitez JAMAIS ces secrets dans Git
- Ne partagez JAMAIS ces secrets publiquement
- Utilisez des mots de passe forts et uniques
- Changez régulièrement vos secrets sensibles
- Limitez l'accès aux secrets GitHub aux personnes de confiance

## Troubleshooting

### Le déploiement échoue avec "Authentication failed"

Vérifiez :
- Les credentials FTP/SSH sont corrects
- Vous pouvez vous connecter manuellement avec ces credentials
- Il n'y a pas d'espaces en trop dans les secrets

### Erreur "Invalid JWT"

Vérifiez :
- Les clés JWT sont complètes (avec BEGIN/END)
- Le JWT_PASSPHRASE correspond à celui utilisé pour générer les clés
- Il n'y a pas de caractères invisibles ou d'espaces en trop

### Erreur de connexion à la base de données

Vérifiez :
- Le format de DATABASE_URL est correct
- Les credentials sont corrects
- La base de données existe sur O2switch
- L'utilisateur a les permissions nécessaires

## Support

Pour toute question sur la configuration des secrets :
- Consultez [DEPLOYMENT.md](../DEPLOYMENT.md)
- Consultez la documentation GitHub Actions : https://docs.github.com/en/actions/security-guides/encrypted-secrets

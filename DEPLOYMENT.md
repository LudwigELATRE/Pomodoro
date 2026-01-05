# Guide de Déploiement sur O2switch

Ce guide explique comment configurer le déploiement automatique sur O2switch via GitHub Actions.

## Prérequis

1. Un compte O2switch avec accès cPanel
2. Un dépôt GitHub pour votre projet
3. Accès SSH activé sur votre hébergement O2switch
4. Une base de données MySQL créée sur O2switch

## Configuration sur O2switch (cPanel)

### 1. Créer la base de données

1. Connectez-vous à cPanel
2. Allez dans **MySQL Databases**
3. Créez une nouvelle base de données (ex: `username_pomodoro`)
4. Créez un utilisateur MySQL avec un mot de passe fort
5. Associez l'utilisateur à la base de données avec tous les privilèges
6. Notez les informations de connexion

### 2. Configurer l'accès SSH

1. Dans cPanel, allez dans **Terminal** ou **SSH Access**
2. Si SSH n'est pas activé, contactez le support O2switch
3. Notez les informations SSH :
   - Host: votre-domaine.com (ou IP du serveur)
   - Port: généralement 22
   - Username: votre nom d'utilisateur cPanel
   - Password: votre mot de passe cPanel

### 3. Préparer le répertoire de déploiement

Via SSH ou le File Manager de cPanel :

```bash
# Se connecter en SSH
ssh username@votre-domaine.com

# Créer les dossiers nécessaires
cd ~/www  # ou public_html selon votre configuration
mkdir -p pomodoro/{var/cache,var/log,config/jwt}
```

### 4. Générer les clés JWT sur le serveur

```bash
cd ~/www/pomodoro

# Installer Composer si nécessaire
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"

# Générer les clés JWT
php bin/console lexik:jwt:generate-keypair

# Récupérer le contenu des clés (pour les GitHub Secrets)
cat config/jwt/private.pem
cat config/jwt/public.pem
```

## Configuration GitHub

### 1. Configurer les Secrets GitHub

Allez dans les paramètres de votre dépôt GitHub :
`Settings > Secrets and variables > Actions > New repository secret`

Ajoutez les secrets suivants :

#### Connexion FTP
- `FTP_SERVER` : ftp.votre-domaine.com
- `FTP_USERNAME` : votre-username-ftp
- `FTP_PASSWORD` : votre-mot-de-passe-ftp
- `FTP_SERVER_DIR` : /www/pomodoro/ (ou le chemin vers votre dossier)

#### Connexion SSH
- `SSH_HOST` : votre-domaine.com
- `SSH_USERNAME` : votre-username-ssh
- `SSH_PASSWORD` : votre-mot-de-passe-ssh
- `SSH_PORT` : 22
- `REMOTE_PATH` : /home/username/www/pomodoro

#### Configuration Symfony
- `APP_SECRET` : Générer avec `php bin/console secrets:generate-keys` ou `openssl rand -hex 32`
- `DATABASE_URL` : `mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=10.11.2-MariaDB&charset=utf8mb4`

#### JWT
- `JWT_PASSPHRASE` : Votre passphrase JWT (utilisée lors de la génération des clés)
- `JWT_PRIVATE_KEY` : Contenu complet de `config/jwt/private.pem`
- `JWT_PUBLIC_KEY` : Contenu complet de `config/jwt/public.pem`

#### CORS et Frontend
- `CORS_ALLOW_ORIGIN` : `^https?://(votre-domaine\.com)(:[0-9]+)?$`
- `FRONTEND_URL` : `https://votre-domaine.com`

#### Google OAuth (optionnel)
- `GOOGLE_CLIENT_ID` : Votre Google Client ID
- `GOOGLE_CLIENT_SECRET` : Votre Google Client Secret

#### Mailer (optionnel)
- `MAILER_DSN` : `smtp://user:pass@smtp.example.com:port`

### 2. Exemple de valeurs pour DATABASE_URL

Format complet :
```
mysql://username:password@host:port/database_name?serverVersion=version&charset=utf8mb4
```

Exemple O2switch :
```
mysql://cpanel_username_pomodoro:mot_de_passe@127.0.0.1:3306/cpanel_username_pomodoro?serverVersion=10.11.2-MariaDB&charset=utf8mb4
```

### 3. Exemple de valeurs pour JWT_PRIVATE_KEY

Le secret doit contenir **tout** le contenu du fichier, y compris les lignes `-----BEGIN RSA PRIVATE KEY-----` et `-----END RSA PRIVATE KEY-----`.

Exemple :
```
-----BEGIN RSA PRIVATE KEY-----
MIIJKQIBAAKCAgEA...
[contenu de la clé]
...
-----END RSA PRIVATE KEY-----
```

## Structure des fichiers sur le serveur

```
~/www/pomodoro/
├── bin/
├── config/
│   ├── jwt/
│   │   ├── private.pem
│   │   └── public.pem
│   └── packages/
├── migrations/
├── public/
│   ├── build/          # Assets compilés
│   └── index.php
├── src/
├── templates/
├── var/
│   ├── cache/
│   └── log/
├── vendor/
└── .env.local
```

## Configuration Apache (O2switch)

Si nécessaire, créez un fichier `.htaccess` dans le dossier racine de votre domaine :

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Rediriger vers le dossier public de Symfony
    RewriteCond %{REQUEST_URI} !^/pomodoro/public/
    RewriteRule ^(.*)$ /pomodoro/public/$1 [L]

    # Rediriger vers index.php
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ /pomodoro/public/index.php [QSA,L]
</IfModule>
```

Ou si votre domaine pointe directement vers `pomodoro/public/` :

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    RewriteCond %{REQUEST_URI}::$0 ^(/.+)/(.*)::\2$
    RewriteRule .* - [E=BASE:%1]

    RewriteCond %{HTTP:Authorization} .+
    RewriteRule ^ - [E=HTTP_AUTHORIZATION:%0]

    RewriteCond %{ENV:REDIRECT_STATUS} =""
    RewriteRule ^index\.php(?:/(.*)|$) %{ENV:BASE}/$1 [R=301,L]

    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ %{ENV:BASE}/index.php [L]
</IfModule>
```

## Vérification de la configuration PHP

Vérifiez que votre hébergement O2switch a les extensions PHP nécessaires :

```bash
php -m | grep -E '(ctype|iconv|intl|pdo_mysql|json|mbstring|xml)'
```

Extensions requises :
- ctype
- iconv
- intl
- pdo_mysql
- json
- mbstring
- xml
- tokenizer

Si une extension manque, contactez le support O2switch.

## Déploiement

### Déploiement automatique

Le déploiement se lance automatiquement lors d'un push sur la branche `main` :

```bash
git add .
git commit -m "Deploy to production"
git push origin main
```

### Déploiement manuel

Vous pouvez aussi déclencher le déploiement manuellement :

1. Allez sur GitHub : `Actions > Deploy to O2switch > Run workflow`
2. Sélectionnez la branche `main`
3. Cliquez sur `Run workflow`

## Étapes du déploiement

Le workflow GitHub Actions effectue les étapes suivantes :

1. **Build** :
   - Installation des dépendances Composer (production)
   - Installation des dépendances npm
   - Compilation des assets avec Webpack Encore
   - Génération du fichier `.env.local` de production
   - Création des clés JWT
   - Nettoyage du cache Symfony

2. **Déploiement FTP** :
   - Upload des fichiers vers le serveur O2switch
   - Exclusion des fichiers de développement

3. **Post-déploiement SSH** :
   - Création des dossiers nécessaires
   - Configuration des permissions
   - Exécution des migrations de base de données
   - Nettoyage et warmup du cache Symfony

## Post-déploiement

### 1. Vérifier l'installation

Accédez à votre site : `https://votre-domaine.com`

### 2. Créer le premier utilisateur

Via SSH :
```bash
cd ~/www/pomodoro
php bin/console app:create-user admin@example.com --admin
```

### 3. Vérifier les logs

En cas d'erreur, consultez les logs :
```bash
tail -f var/log/prod.log
```

## Maintenance

### Mettre à jour l'application

Simplement pusher sur la branche `main` déclenchera un nouveau déploiement.

### Exécuter des commandes manuellement

```bash
# Se connecter en SSH
ssh username@votre-domaine.com
cd ~/www/pomodoro

# Vider le cache
php bin/console cache:clear --env=prod

# Exécuter des migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Vérifier le statut de la base
php bin/console doctrine:schema:validate
```

### Sauvegardes

Il est recommandé de configurer des sauvegardes automatiques :

1. **Base de données** : Utiliser les outils de backup de cPanel
2. **Fichiers** : Sauvegarder le dossier `config/jwt/` et `.env.local`

## Troubleshooting

### Erreur 500 Internal Server Error

1. Vérifiez les logs : `var/log/prod.log`
2. Vérifiez les permissions :
   ```bash
   chmod -R 775 var/
   ```
3. Videz le cache :
   ```bash
   php bin/console cache:clear --env=prod
   ```

### Erreur de connexion à la base de données

1. Vérifiez le `DATABASE_URL` dans les secrets GitHub
2. Testez la connexion :
   ```bash
   php bin/console doctrine:schema:validate
   ```
3. Vérifiez que l'utilisateur MySQL a les bons privilèges

### Erreur JWT

1. Vérifiez que les clés JWT existent et ont les bonnes permissions :
   ```bash
   ls -la config/jwt/
   chmod 600 config/jwt/private.pem
   chmod 644 config/jwt/public.pem
   ```
2. Vérifiez que `JWT_PASSPHRASE` correspond à celle utilisée pour générer les clés

### Assets non chargés

1. Vérifiez que le dossier `public/build/` existe et contient les fichiers
2. Vérifiez les permissions :
   ```bash
   chmod -R 755 public/build/
   ```
3. Vérifiez la configuration Apache/htaccess

## Sécurité

### Recommandations

1. **Ne jamais committer** les fichiers :
   - `.env.local`
   - `config/jwt/private.pem`
   - `config/jwt/public.pem`

2. **Utiliser HTTPS** : Configurez SSL/TLS sur O2switch (Let's Encrypt gratuit)

3. **Mots de passe forts** : Utilisez des mots de passe complexes pour :
   - Base de données
   - FTP/SSH
   - JWT_PASSPHRASE
   - APP_SECRET

4. **Limiter l'accès SSH** : Utilisez une clé SSH au lieu d'un mot de passe si possible

5. **Sauvegardes régulières** : Configurez des backups automatiques

## Support

- Documentation Symfony : https://symfony.com/doc/current/deployment.html
- Support O2switch : https://www.o2switch.fr/support/
- GitHub Actions : https://docs.github.com/en/actions

## Checklist de déploiement

- [ ] Base de données créée sur O2switch
- [ ] Clés JWT générées
- [ ] Tous les secrets GitHub configurés
- [ ] Fichier `.htaccess` configuré
- [ ] Domaine pointe vers le bon dossier
- [ ] SSL/TLS activé
- [ ] Premier déploiement réussi
- [ ] Application accessible en HTTPS
- [ ] Tests de connexion réussis
- [ ] Monitoring configuré
- [ ] Sauvegardes planifiées

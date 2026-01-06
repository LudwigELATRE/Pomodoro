# Configuration du Workflow GitHub Actions

Le fichier workflow `.github/workflows/deploy.yml` n'a pas pu être poussé via la ligne de commande car votre Personal Access Token (PAT) n'a pas le scope `workflow` requis.

## Option 1 : Ajouter le workflow via l'interface web GitHub (Recommandé)

### Étapes :

1. **Allez sur votre dépôt GitHub** : https://github.com/LudwigELATRE/Pomodoro

2. **Créez le workflow** :
   - Cliquez sur l'onglet `Actions`
   - Cliquez sur `New workflow`
   - Cliquez sur `set up a workflow yourself`

3. **Copiez le contenu** :
   - Ouvrez le fichier `.github/workflows/deploy.yml` depuis votre machine locale
   - Copiez tout son contenu
   - Collez-le dans l'éditeur GitHub

4. **Enregistrez** :
   - Nommez le fichier : `.github/workflows/deploy.yml`
   - Cliquez sur `Commit changes...`
   - Ajoutez un message de commit : "Add deployment workflow for O2switch"
   - Cliquez sur `Commit changes`

## Option 2 : Mettre à jour votre Personal Access Token

Si vous préférez pusher via la ligne de commande à l'avenir :

### Étapes :

1. **Créez un nouveau token** :
   - Allez sur GitHub : https://github.com/settings/tokens
   - Cliquez sur `Generate new token` > `Generate new token (classic)`
   - Donnez un nom au token : "Pomodoro Deployment"
   - Sélectionnez les scopes suivants :
     - ✅ `repo` (Full control of private repositories)
     - ✅ `workflow` (Update GitHub Action workflows)
   - Générez et copiez le token

2. **Configurez le nouveau token** :
   ```bash
   # Mettre à jour le remote avec le nouveau token
   git remote set-url origin https://YOUR_TOKEN@github.com/LudwigELATRE/Pomodoro.git

   # Ou configurez git credential helper pour stocker le token
   git config credential.helper store
   ```

3. **Poussez le workflow** :
   ```bash
   git add .github/workflows/deploy.yml
   git commit -m "Add deployment workflow for O2switch"
   git push origin main
   ```

## Vérification

Une fois le workflow ajouté (via l'une des deux options) :

1. Allez sur `Actions` dans votre dépôt GitHub
2. Vous devriez voir le workflow "Deploy to O2switch"
3. Vous pouvez le tester en cliquant sur `Run workflow`

## Configuration des Secrets

Avant de pouvoir utiliser le workflow, configurez les secrets GitHub en suivant :
- [.github/SECRETS_TEMPLATE.md](.github/SECRETS_TEMPLATE.md)
- [DEPLOYMENT.md](DEPLOYMENT.md)

## Contenu du fichier workflow

Le fichier `.github/workflows/deploy.yml` est disponible localement dans votre projet.

Vous pouvez aussi le consulter en ligne après l'avoir ajouté à GitHub.

## Support

Pour toute question :
- Consultez [DEPLOYMENT.md](DEPLOYMENT.md)
- Documentation GitHub Actions : https://docs.github.com/en/actions

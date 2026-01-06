.PHONY: help install install-local start stop restart db-create db-migrate db-reset cache-clear logs deploy-check

help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

install: ## Installation complète du projet (avec Docker)
	composer install
	npm install
	docker compose up -d database
	@echo "Attente du démarrage de la base de données..."
	@sleep 5
	php bin/console doctrine:migrations:migrate --no-interaction
	@echo "✅ Installation terminée!"

install-local: ## Installation complète du projet (sans Docker)
	composer install
	npm install
	php bin/console doctrine:database:create --if-not-exists
	php bin/console doctrine:migrations:migrate --no-interaction
	php bin/console lexik:jwt:generate-keypair --skip-if-exists
	@echo "✅ Installation terminée!"

start: ## Démarre tous les services
	docker compose up -d
	@echo "✅ Services démarrés"

stop: ## Arrête tous les services
	docker compose down
	@echo "✅ Services arrêtés"

restart: stop start ## Redémarre tous les services

db-create: ## Crée la base de données
	php bin/console doctrine:database:create --if-not-exists

db-migrate: ## Lance les migrations
	php bin/console doctrine:migrations:migrate --no-interaction

db-reset: ## Reset complet de la base de données
	php bin/console doctrine:database:drop --force --if-exists
	php bin/console doctrine:database:create
	php bin/console doctrine:migrations:migrate --no-interaction
	@echo "✅ Base de données réinitialisée"

db-fixtures: ## Charge les fixtures (si vous en avez)
	php bin/console doctrine:fixtures:load --no-interaction

cache-clear: ## Vide le cache Symfony
	php bin/console cache:clear
	@echo "✅ Cache vidé"

logs: ## Affiche les logs Symfony
	tail -f var/log/dev.log

dev: ## Lance l'environnement de développement complet
	@echo "🚀 Démarrage de l'environnement de développement..."
	@make db-migrate
	@symfony server:start -d || php -S localhost:8000 -t public/ &
	@npm run watch

build: ## Build les assets frontend
	npm run build

build-prod: ## Build les assets pour la production
	npm run build
	php bin/console cache:clear --env=prod
	php bin/console cache:warmup --env=prod

test: ## Lance les tests
	php bin/phpunit

cs-fix: ## Corrige le code avec PHP-CS-Fixer
	vendor/bin/php-cs-fixer fix

quality: cs-fix test ## Lance les outils de qualité de code

jwt-generate: ## Génère les clés JWT
	php bin/console lexik:jwt:generate-keypair

setup-deployment: ## Configure les secrets pour le déploiement (génère JWT, APP_SECRET, etc.)
	@bash scripts/setup-deployment.sh

deploy-check: ## Vérifie que tout est prêt pour le déploiement
	@echo "🔍 Vérification de la configuration de déploiement..."
	@test -f .env.local && echo "✅ .env.local existe" || echo "❌ .env.local manquant"
	@test -f config/jwt/private.pem && echo "✅ Clé JWT privée existe" || echo "❌ Clé JWT privée manquante"
	@test -f config/jwt/public.pem && echo "✅ Clé JWT publique existe" || echo "❌ Clé JWT publique manquante"
	@php bin/console doctrine:schema:validate && echo "✅ Schéma de base de données valide" || echo "❌ Schéma de base de données invalide"
	@npm run build && echo "✅ Build frontend réussi" || echo "❌ Build frontend échoué"
	@echo "📋 Vérifiez les secrets GitHub pour le déploiement (voir DEPLOYMENT.md)"

serve: ## Démarre le serveur Symfony
	symfony server:start || php -S localhost:8000 -t public/

serve-bg: ## Démarre le serveur Symfony en arrière-plan
	symfony server:start -d || (php -S localhost:8000 -t public/ > /dev/null 2>&1 &)

watch: ## Lance le watch des assets
	npm run watch

# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Pomodoro Monolith is a full-stack Pomodoro timer application with a Symfony 7 backend and Vue 3 frontend, bundled via Webpack Encore.

## Common Commands

### Development
```bash
make install-local     # Full local installation (without Docker)
make dev               # Start full dev environment (runs migrations + server + watch)
make serve             # Start Symfony server (uses symfony CLI or PHP built-in)
npm run watch          # Watch frontend assets with hot reload
npm run build          # Production build
```

### Database
```bash
make db-migrate        # Run migrations
make db-reset          # Drop, recreate, and migrate database
php bin/console doctrine:migrations:diff   # Generate new migration
```

### Testing
```bash
make test              # Run PHPUnit tests (php bin/phpunit)
```

### Other
```bash
make cache-clear       # Clear Symfony cache
make jwt-generate      # Generate JWT keypair
make deploy-check      # Verify deployment readiness
```

## Architecture

### Backend (Symfony 7 / PHP 8.3)
- **Entities** (`src/Entity/`): `User`, `PomodoroSession`, `UserSettings`
- **Controllers** (`src/Controller/`): REST API controllers for auth, pomodoro sessions, statistics, settings
- **Authentication**: JWT (Lexik JWT Bundle) + Google OAuth support
- **API routes** are prefixed with `/api` and require JWT authentication (except `/api/login`, `/api/register`, `/api/auth/google`)

### Frontend (Vue 3)
- Entry point: `assets/js/app.js`
- Vue app: `assets/vue/`
- **State management**: Pinia stores in `assets/vue/stores/` (`auth.js`, `pomodoro.js`)
- **Router**: `assets/vue/router/index.js` (uses vue-router with auth guards)
- **API services**: `assets/vue/services/` (auth, pomodoro, api)
- **i18n**: Supports English and French (`assets/vue/i18n/locales/`)
- **Styling**: Tailwind CSS via PostCSS

### Build System
- Webpack Encore configured in `webpack.config.js`
- Vue loader and PostCSS (Tailwind) enabled
- Output to `public/build/`

## Key Patterns

### API Authentication Flow
1. Frontend stores JWT token in localStorage
2. API calls include token via axios interceptor
3. Backend validates JWT on `/api/*` routes (except public auth endpoints)
4. User info available via `$this->getUser()` in controllers

### Pomodoro Session Types
- `work`: Work session (default 25 min)
- `short_break`: Short break (default 5 min)
- `long_break`: Long break after N pomodoros (default 15 min)

## Deployment

Automated via GitHub Actions (`.github/workflows/deploy.yml`) on push to `main`:
- Builds PHP dependencies and frontend assets
- Deploys via FTP to O2switch
- Runs migrations and cache warmup via SSH

# AGENTS.md

This file provides guidance to AI agents when working with code in this repository.

## Project Context

**Engineering Team Applicant Exercise** - A technical assessment for Publica.la candidates.

For exercise requirements and instructions, refer to the **README.md** file.

## Technology Stack

- **Laravel 12** with PHP 8.3+
- **PostgreSQL 15** via Docker
- **Vite** for asset bundling (replaced webpack)
- **Laravel Sail** for Docker environment
- **Bootstrap** via Laravel UI

## Essential Commands

```bash
# Environment Setup
cp .env.example .env
composer install
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail yarn install
./vendor/bin/sail artisan storage:link

# Development
./vendor/bin/sail yarn dev          # Start Vite dev server with HMR
./vendor/bin/sail yarn build         # Build production assets
./vendor/bin/sail artisan serve      # Alternative local server

# Testing
./vendor/bin/sail artisan test       # Run all tests
./vendor/bin/sail artisan test --filter=SearchTest  # Run specific test
./vendor/bin/sail artisan test tests/Unit           # Run unit tests only
./vendor/bin/sail artisan test tests/Feature        # Run feature tests only

# Database (if implementing database search)
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan migrate:fresh
./vendor/bin/sail artisan tinker     # Interactive REPL

# Container Management
./vendor/bin/sail up -d               # Start containers
./vendor/bin/sail down                # Stop containers
./vendor/bin/sail bash                # Enter container shell
./vendor/bin/sail logs -f             # Stream logs
```

## Exercise Data Location

Book data files are located in `storage/exercise-files/`


## Common Pitfalls to Avoid

- Don't forget to run `storage:link` for serving files
- Vite requires `yarn dev` running during development
- PostgreSQL runs on port 5432 inside Docker network
- Use `./vendor/bin/sail` prefix for all container commands
- Assets must be built with `yarn build` for production


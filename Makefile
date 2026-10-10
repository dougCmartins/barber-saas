COMPOSE := $(shell docker compose version >/dev/null 2>&1 && echo docker compose || echo docker-compose)

.PHONY: up down logs migrate seed test shell-api test-web shell-web

up:
	$(COMPOSE) up --build

down:
	$(COMPOSE) down

logs:
	$(COMPOSE) logs -f

migrate:
	$(COMPOSE) exec backend php artisan migrate --force

seed:
	$(COMPOSE) exec backend php artisan db:seed --force

test:
	$(COMPOSE) exec backend php artisan test

shell-api:
	$(COMPOSE) exec backend bash

test-web:
	$(COMPOSE) exec frontend npm run test:unit -- --run

shell-web:
	$(COMPOSE) exec frontend sh

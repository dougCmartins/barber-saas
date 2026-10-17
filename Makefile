COMPOSE := $(shell docker compose version >/dev/null 2>&1 && echo docker compose || echo docker-compose)
HOST_UID := $(shell id -u)
HOST_GID := $(shell id -g)

export HOST_UID
export HOST_GID

.PHONY: up down logs migrate seed test shell-api test-web shell-web permissions

up: permissions
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

permissions:
	$(COMPOSE) run --rm --no-deps --user root --entrypoint sh backend -c 'sh docker/fix-permissions.sh'

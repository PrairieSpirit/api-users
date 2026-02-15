.PHONY: up down build install migrate test shell dump-db clean reset-db reset-test-db schema-update build-dc up-dc down-dc cli-dc cli-doc

# Автоматичне визначення Docker Compose команди
DOCKER_COMPOSE := $(shell if command -v docker-compose >/dev/null 2>&1; then echo "docker-compose"; else echo "docker compose"; fi)

# --- Docker Compose базові команди ---
build-dc:
	$(DOCKER_COMPOSE) build

up-dc:
	$(DOCKER_COMPOSE) up -d

down-dc:
	$(DOCKER_COMPOSE) down --remove-orphans

cli-dc:
	$(DOCKER_COMPOSE) exec php sh

cli-doc:
	docker exec -it api_users_php sh

# --- Default ---
up:
	$(DOCKER_COMPOSE) up -d

down:
	$(DOCKER_COMPOSE) down

clean: down
	$(DOCKER_COMPOSE) down -v

build:
	$(DOCKER_COMPOSE) build

install: up
	$(DOCKER_COMPOSE) exec php composer install --no-interaction

migrate: install
	$(DOCKER_COMPOSE) exec php php bin/console doctrine:database:create --if-not-exists
	$(DOCKER_COMPOSE) exec php php bin/console doctrine:schema:create --no-interaction

schema-update: install
	$(DOCKER_COMPOSE) exec php php bin/console doctrine:schema:update --force --no-interaction

test:
	$(DOCKER_COMPOSE) exec mysql \
		mysql -uroot -proot \
		-e "CREATE DATABASE IF NOT EXISTS api_users_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

	$(DOCKER_COMPOSE) exec php sh -c 'APP_ENV=test php bin/console doctrine:schema:update --force --no-interaction --env=test'
	$(DOCKER_COMPOSE) exec php sh -c 'APP_ENV=test php bin/phpunit'

shell:
	$(DOCKER_COMPOSE) exec php sh

reset-test-db:
	$(DOCKER_COMPOSE) exec mysql \
		mysql -uroot -proot \
		-e "DROP DATABASE IF EXISTS api_users_test; CREATE DATABASE api_users_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

	$(DOCKER_COMPOSE) exec php sh -c 'APP_ENV=test php bin/console doctrine:schema:create --no-interaction'
	$(DOCKER_COMPOSE) exec php sh -c 'APP_ENV=test php bin/console doctrine:fixtures:load --no-interaction'

dump-db:
	mkdir -p sql
	$(DOCKER_COMPOSE) exec mysql \
		mysqldump -uroot -proot symfony > sql/database_dump_$$(date +%Y%m%d_%H%M%S).sql
	@echo "Dump saved to sql/database_dump_<timestamp>.sql"


reset-db: install
	$(DOCKER_COMPOSE) exec php php bin/console doctrine:database:drop --force --if-exists
	$(DOCKER_COMPOSE) exec php php bin/console doctrine:database:create
	$(DOCKER_COMPOSE) exec php php bin/console doctrine:schema:create --no-interaction
	$(DOCKER_COMPOSE) exec php php bin/console doctrine:fixtures:load --no-interaction

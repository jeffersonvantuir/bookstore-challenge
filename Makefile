DOCKER_COMP = docker compose
PHP_CONT = $(DOCKER_COMP) exec bookstore_php

build:
	@$(DOCKER_COMP) build

down:
	@$(DOCKER_COMP) down

stop:
	@$(DOCKER_COMP) stop

up-d:
	@$(DOCKER_COMP) up --detach $(c)

dev:
	@$(DOCKER_COMP) down
	@$(DOCKER_COMP) up -d

php-bash:
	@$(PHP_CONT) bash

lint:
	@$(PHP_CONT) ./vendor/bin/phpstan
	@$(PHP_CONT) ./vendor/bin/phpcs

test:
	@$(PHP_CONT) ./vendor/bin/phpunit

test-cov:
	@$(DOCKER_COMP) exec -e XDEBUG_MODE=coverage bookstore_php vendor/bin/phpunit --coverage-html coverage/html
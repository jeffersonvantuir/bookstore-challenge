DOCKER_COMP = docker compose
PHP_CONT = $(DOCKER_COMP) exec bookstore_php

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

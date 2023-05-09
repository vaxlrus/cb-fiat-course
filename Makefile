DOCKER_COMPOSE := $(shell command -v docker-compose || echo 'docker compose')

install:
	$(DOCKER_COMPOSE) build
	$(DOCKER_COMPOSE) run --rm php composer install
    $(DOCKER_COMPOSE) run --rm php php artisan key:generate
    $(DOCKER_COMPOSE) run --rm php artisan storage:link
	$(DOCKER_COMPOSE) up -d
	until $(DOCKER_COMPOSE) exec database pg_isready -U postgres; do sleep 0.5; done
	$(DOCKER_COMPOSE) exec php php artisan migrate
	$(DOCKER_COMPOSE) exec php php artisan db:seed
	$(DOCKER_COMPOSE) down

.PHONY: help
SAIL = ./vendor/bin/sail
CONTAINER_NAME=laravel-toko-online
VOLUME_DATABASE=toko-online_db-vol

help: ## Print help.
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n\nTargets:\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-10s\033[0m %s\n", $$1, $$2 }' $(MAKEFILE_LIST)


ps: ## Docker containers.
	@docker compose ps

build: ## Build all containers.

	@docker build --no-cache .

start: ## Start all containers.
	@${SAIL} up -d

stop: ## Stop all containers.
	@${SAIL} stop

destroy: ##Destroy all containers
	@${SAIL} down

setup: start migrate-fresh db-seed storage-link optimize ## Destroy containers, build images, start containers.

fresh: stop destroy build start   ## Destroy containers, build images, start containers.

fresh-data: fresh migrate db-seed storage-link cache ## Destroy containers, build images, start containers.

migrate: ## Run migrations file
	@${SAIL} artisan migrate

migrate-fresh: ## Clear Database and run migrations file
	@${SAIL} artisan migrate:fresh

db-seed: ## Seed database
	@${SAIL} artisan db:seed

optimize: ##cache project
	@${SAIL} artisan optimize

cache: ## clear Cache Project
	@${SAIL} artisan cache:clear

storage-link: ## Cache Project
	@${SAIL} artisan storage:link


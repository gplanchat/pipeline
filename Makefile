
ENVIRONMENT=default
TLS_VERIFY=$(shell docker-machine env $(ENVIRONMENT) | grep 'DOCKER_TLS_VERIFY=".*"' | cut -d\" -f2)
HOST=$(shell docker-machine env $(ENVIRONMENT) | grep 'DOCKER_HOST=".*"' | cut -d\" -f2)
CERT_PATH=$(shell docker-machine env $(ENVIRONMENT) | grep 'DOCKER_CERT_PATH=".*"' | cut -d\" -f2)
MACHINE_NAME=$(shell docker-machine env $(ENVIRONMENT) | grep 'DOCKER_MACHINE_NAME=".*"' | cut -d\" -f2)
export DOCKER_MACHINE_NAME=$(MACHINE_NAME)
export DOCKER_TLS_VERIFY=$(TLS_VERIFY)
export DOCKER_HOST=$(HOST)
export DOCKER_CERT_PATH=$(CERT_PATH)

CLI=docker-compose run --rm cli
COMPOSER=$(CLI) composer
CONSOLE=$(CLI) bin/console --env=prod

.PHONY: up
up: init-sync build-dev
	docker-sync-stack start

.PHONY: down
down: stop-sync
	docker-compose down

.PHONY: init-sync
init-sync:
	$(MAKE) -C .docker install-docker-sync

.PHONY: stop-sync
stop-sync:
	docker-sync stop

.PHONY: build-machine
build-machine:
	$(MAKE) -C .docker $@

.PHONY: rebuild-machine
rebuild-machine:
	$(MAKE) -C .docker $@

.PHONY: clear-machine
clear-machine:
	$(MAKE) -C .docker $@

.PHONY: build
build: build-prod

.PHONY: build-prod
build-prod:
	$(MAKE) -C .docker $@

.PHONY: build-dev
build-dev:
	$(MAKE) -C .docker $@

.PHONY: install
install: init-sync build-dev
	docker-compose up -d sql mail
	$(COMPOSER) install --prefer-dist
	$(CONSOLE) oro:install \
		--organization-name=Kiboko \
		--user-name=admin \
		--user-firstname=Hippo \
		--user-lastname=Potamus \
		--user-email=hello@kiboko.fr \
		--user-password=password \
		--sample-data \
		--symlink \
		--timeout=0

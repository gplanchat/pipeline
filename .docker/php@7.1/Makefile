.PHONY: build
build: build-dev build-prod

.PHONY: build-dev
build-dev: build-cli-dev build-fpm-dev

.PHONY: build-prod
build-prod: build-cli-prod build-fpm-prod

.PHONY: build-cli-dev
build-cli-dev:
	$(MAKE) -C cli/ build-dev

.PHONY: build-cli-prod
build-cli-prod:
	$(MAKE) -C cli/ build-prod

.PHONY: build-fpm-dev
build-fpm-dev:
	$(MAKE) -C fpm/ build-dev

.PHONY: build-fpm-prod
build-fpm-prod:
	$(MAKE) -C fpm/ build-prod

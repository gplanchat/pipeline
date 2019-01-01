VM_CPU_COUNT := 2
VM_MEMORY_SIZE := 4096
VM_DISK_SIZE := 40000
VM_NAME := default

BREW_PREFIX := $(shell brew --prefix)
UNAME := $(shell uname)

ifeq ($(OS),Windows_NT)
VM_DRIVER=hyperv
HOST_OS=windows
else ifeq ($(UNAME),Darwin)
VM_DRIVER=xhyve
HOST_OS=osx
else ifeq ($(UNAME),Linux)
VM_DRIVER=
HOST_OS=linux
else
VM_DRIVER=
HOST_OS=
endif

.PHONY: build-machine
build-machine:
ifeq ($(HOST_OS),osx)
    ifeq ($(VM_DRIVER),xhyve)
		$(MAKE) create-mac-xhyve-machine
    else ifeq ($(VM_DRIVER),virtualbox)
		$(MAKE) create-generic-vbox-machine
    else
        $(error Unupported driver: "$(VM_DRIVER)", should be "xhyve" or "virtualbox")
    endif
else ifeq ($(HOST_OS),windows)
    ifeq ($(VM_DRIVER),hyperv)
		$(MAKE) create-windows-hyperv-machine
    else ifeq ($(VM_DRIVER),virtualbox)
		$(MAKE) create-generic-vbox-machine
    else
        $(error Unupported driver: "$(VM_DRIVER)", should be "hyperv" or "virtualbox")
    endif
else
    $(error Unupported host OS: "$(VM_DRIVER)", should be "osx" or "windows")
endif

.PHONY: clear-machine
clear-machine:
	docker-machine rm --force $(VM_NAME)

.PHONY: rebuild-machine
rebuild-machine: clear-machine build-machine

.PHONY: install-mac-xhyve-driver
install-mac-xhyve-driver:
ifeq (, $(shell brew ls --versions docker-machine-driver-xhyve))
	brew install docker-machine-driver-xhyve
	sudo chown root:wheel $(BREW_PREFIX)/opt/docker-machine-driver-xhyve/bin/docker-machine-driver-xhyve
	sudo chmod u+s $(BREW_PREFIX)/opt/docker-machine-driver-xhyve/bin/docker-machine-driver-xhyve
else
	-brew upgrade docker-machine-driver-xhyve
endif

.PHONY: install-docker-sync
install-docker-sync:
ifeq (, $(shell which docker-sync))
	gem install docker-sync
endif
ifeq ($(HOST_OS),osx)
    ifeq (, $(shell which unison))
		brew install unison
		brew install eugenmayer/dockersync/unox
    endif
endif

.PHONY: create-mac-xhyve-machine
create-mac-xhyve-machine: install-mac-xhyve-driver
	docker-machine create \
		--xhyve-cpu-count=$(VM_CPU_COUNT) \
		--xhyve-memory-size=$(VM_MEMORY_SIZE) \
		--xhyve-disk-size=$(VM_DISK_SIZE) \
		--xhyve-experimental-nfs-share=true \
		--xhyve-boot2docker-url https://github.com/boot2docker/boot2docker/releases/download/v18.09.1-rc1/boot2docker.iso \
		--driver xhyve \
		$(VM_NAME)

.PHONY: create-windows-hyperv-machine
create-windows-hyperv-machine:
	docker-machine create \
		--hyperv-cpu-count=$(VM_CPU_COUNT) \
		--hyperv-memory=$(VM_MEMORY_SIZE) \
		--hyperv-disk-size=$(VM_DISK_SIZE) \
		--driver hyperv \
		$(VM_NAME)

.PHONY: create-generic-vbox-machine
create-generic-vbox-machine:
	docker-machine create \
		--virtualbox-cpu-count=$(VM_CPU_COUNT) \
		--virtualbox-memory=$(VM_MEMORY_SIZE) \
		--virtualbox-disk-size=$(VM_DISK_SIZE) \
		--driver virtualbox \
		--virtualbox-ui-type headless \
		$(VM_NAME)

.PHONY: build
build: build-sql build-php build-http

.PHONY: build-dev
build-dev: build-sql build-php-dev build-http-dev

.PHONY: build-prod
build-prod: build-sql build-php-prod build-http-prod

.PHONY: build-sql
build-sql:
	$(MAKE) -C postgres@9.6/

.PHONY: build-php
build-php:
	$(MAKE) -C php@7.1

.PHONY: build-php-dev
build-php-dev:
	$(MAKE) -C php@7.1 build-dev

.PHONY: build-php-prod
build-php-prod:
	$(MAKE) -C php@7.1 build-prod

.PHONY: build-http
build-http:
	$(MAKE) -C nginx@1.15

.PHONY: build-http-dev
build-http-dev:
	$(MAKE) -C nginx@1.15 build-dev

.PHONY: build-http-prod
build-http-prod:
	$(MAKE) -C nginx@1.15 build-prod

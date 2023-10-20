LINUX_SHELL ?=/bin/bash
ENV_FILE = .env
RUN_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
.PHONY: list
list:
	$(call help)
.PHONY: help
help:
	$(call help)
	
up:
	$(call up)
up/network:
	$(call upNetwork)
up/traefik:
	$(call upTraefik)
up/web:
	$(call upPlatform,web)

down:
	$(call down)
down/traefik:
	$(call downTraefik)
down/web:
	$(call downPlatform,web)

mount/apache:
	$(call mount_container,xm,apache)
mount/mysql:
	$(call mount_container,mysql)


build:
	$(call build_all)
build/web/apache:
	$(call build,web,apache)
build/web/mysql:
	$(call build,web,mysql)
build/traefik:
	$(call build_all,traefik)

logs/web/apache:
	$(call container_log,web-apache)
logs/web/mysql:
	$(call container_log,web-mysql)

define mount_container
	$(call setup_env,$(ENV_FILE))
	@docker exec -it $(1)-${CONFIG}-$(2) $(LINUX_SHELL)
endef

define container_log
	@docker logs -f $(1)
endef

define build
	@set -a
	$(call setup_env,$(ENV_FILE))
	@cd $1 && docker-compose --env-file $(ENV_FILE) build --build-arg platform=$(PLATFORM) $(2)
endef

define build_all
	@set -a
	$(call setup_env,$(ENV_FILE))
	@docker-compose --env-file $(ENV_FILE) build --build-arg platform=$(PLATFORM)
endef

define setup_env
	$(shell test ! -f $(1) && echo $(ENV_FILE) file does not exist && exit)
    $(eval ENV_FILE := $(1))
    $(eval include $(1))
    $(eval export sed 's/=.*//' $(1))
endef

define down
	@set -a
	$(call source $(ENV_FILE))
	@echo === down app ===
	$(call downPlatform)
	@echo === down traefik ===
	$(call downTraefik)
	@echo === list running containers ===
	@docker ps
endef

define downOne
	@set -a
	$(call source $(ENV_FILE))
	cd $(1) && docker compose  --env-file $(ENV_FILE) stop $(2)
endef

define upOne
	@set -a
	@$(call source $(ENV_FILE))
	@cd $(1) && docker-compose up --no-start $(2)
	@cd $(1) && docker-compose  --env-file $(ENV_FILE)  start $(2)
endef

define upNetwork
	$(call source $(ENV_FILE))
endef

define upTraefik
	@set -a
	$(call source ../$(ENV_FILE))
	cd ./traefik && ./st 
endef

define downTraefik
	@set -a
	$(call source ../$(ENV_FILE))
	@cd ./traefik && ./dn 
endef


define upPlatform
	@set -a
	$(call source $(ENV_FILE))
	@docker compose  --env-file $(ENV_FILE)  up -d
endef

define downPlatform
	@set -a
	$(call source $(ENV_FILE))
	@docker compose  --env-file $(ENV_FILE) down
endef


define up
	@set -a
	$(call source $(ENV_FILE))
	@echo === launch network ===
	@./scripts/network
	@echo === launch traefik proxy ===
	$(call upTraefik)
	@echo === launch web ===
	$(call upPlatform)
	@echo === list stopped containers ===
	@sleep 1
	@docker ps -f "status=exited"
endef

define upProfile
	@set -a
	$(call source $(ENV_FILE))
	@COMPOSE_PROFILES=mysql-$(1),common docker compose up -d
	@echo === list stopped containers ===
	@docker ps -f "status=exited"
endef


define downProfile
	@set -a
	$(call source $(ENV_FILE))
	@COMPOSE_PROFILES=mysql-$(1),common docker compose down
	@echo === list running containers ===
	@docker ps
endef

define import_mysql
	@docker exec $(join mysql-,$(1)) /bin/sh -c "cat /sql/*.sql | mysql -u root -proot $(join Bodas_,$(shell echo $(1) | tr 'a-z' 'A-Z'))"
endef

define help
	@LC_ALL=C $(MAKE) -pRrq -f $(firstword $(MAKEFILE_LIST)) : 2>/dev/null | awk -v RS= -F: '/(^|\n)# Files(\n|$$)/,/(^|\n)# Finished Make data base/ {if ($$1 !~ "^[#.]") {print $$1}}' | sort | grep -E -v -e '^[^[:alnum:]]' -e '^$@$$'
endef

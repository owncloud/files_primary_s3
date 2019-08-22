# This file is licensed under the GNU General Public License v2.0
# # @author Sergio Bertolin <sbertolin@owncloud.com>

SHELL := /bin/bash

COMPOSER_BIN := $(shell command -v composer 2> /dev/null)
ifndef COMPOSER_BIN
    $(error composer is not available on your system, please install composer)
endif

NPM := $(shell command -v npm 2> /dev/null)
ifndef NPM
    $(error npm is not available on your system, please install npm)
endif

app_name=$(notdir $(CURDIR))
project_directory=$(CURDIR)/../$(app_name)
build_directory=$(CURDIR)/build
scoper_directory=$(build_directory)/scoper
build_tools_directory=$(build_directory)/tools
appstore_build_directory=$(build_directory)/artifacts/appstore
appstore_package_name=$(appstore_build_directory)/$(app_name)

acceptance_test_deps=vendor-bin/behat/vendor

occ=$(CURDIR)/../../occ
private_key=$(HOME)/.owncloud/certificates/$(app_name).key
certificate=$(HOME)/.owncloud/certificates/$(app_name).crt
sign=php -f $(occ) integrity:sign-app --privateKey="$(private_key)" --certificate="$(certificate)"
sign_skip_msg="Skipping signing, either no key and certificate found in $(private_key) and $(certificate) or occ can not be found at $(occ)"
ifneq (,$(wildcard $(private_key)))
ifneq (,$(wildcard $(certificate)))
ifneq (,$(wildcard $(occ)))
	CAN_SIGN=true
endif
endif
endif

# bin file definitions
PHPUNIT=php -d zend.enable_gc=0  "$(PWD)/../../lib/composer/bin/phpunit"
PHPUNITDBG=phpdbg -qrr -d memory_limit=4096M -d zend.enable_gc=0 "$(PWD)/../../lib/composer/bin/phpunit"
PHP_CS_FIXER=php -d zend.enable_gc=0 vendor-bin/owncloud-codestyle/vendor/bin/php-cs-fixer
PHAN=php -d zend.enable_gc=0 vendor-bin/phan/vendor/bin/phan
PHPSTAN=php -d zend.enable_gc=0 vendor-bin/phpstan/vendor/bin/phpstan
PHPSCOPER=php vendor-bin/php-scoper/vendor/bin/php-scoper
BEHAT_BIN=vendor-bin/behat/vendor/bin/behat
ACCEPTANCE_RUNNER?=../../tests/acceptance/run.sh

# start with displaying help
help: ## Show this help message
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//' | sed -e 's/  */ /' | column -t -s :

.PHONY: all
all: vendor

.PHONY: clean
clean: ## Remove appstore build
	rm -rf $(build_directory)
	rm -rf ./vendor
	rm -Rf vendor-bin/**/vendor vendor-bin/**/composer.lock

.PHONY: dist
dist: ## Builds the appstore package
	make appstore

.PHONY: dist-qa
dist-qa: ## Builds the qa package
dist-qa: vendor scope
	rm -rf $(appstore_build_directory)
	mkdir -p $(appstore_package_name)
	cp --parents -r appinfo LICENSE CHANGELOG.md tests $(appstore_package_name)
	cp -r $(scoper_directory)/lib $(appstore_package_name)/lib
	cp -r $(scoper_directory)/vendor $(appstore_package_name)/vendor
	tar --format=gnu -czf $(appstore_package_name).tar.gz -C $(appstore_package_name)/../ $(app_name)

.PHONY: scope
scope: ## Scoper
scope: vendor-bin/php-scoper/vendor
	mkdir -p $(scoper_directory)
	$(PHPSCOPER) add-prefix --output-dir $(scoper_directory) --force --config=./scoper.inc.php
	$(COMPOSER_BIN) dump-autoload --working-dir $(scoper_directory) --classmap-authoritative
	php scoper-fix-autoloader.php

# Builds the package for the app store, ignores php and js tests
.PHONY: appstore
appstore: ## Builds the package for app store
appstore: vendor scope
	rm -rf $(appstore_build_directory)
	mkdir -p $(appstore_package_name)
	cp --parents -r \
	appinfo \
	LICENSE \
	CHANGELOG.md \
	$(appstore_package_name)
	cp -r $(scoper_directory)/lib $(appstore_package_name)/lib
	cp -r $(scoper_directory)/vendor $(appstore_package_name)/vendor

ifdef CAN_SIGN
	$(sign) --path="$(appstore_package_name)"
else
	@echo $(sign_skip_msg)
endif
	tar --format=gnu -czf $(appstore_package_name).tar.gz -C $(appstore_package_name)/../ $(app_name)

##--------------------
## Tests
##--------------------

.PHONY: test-php-codecheck
test-php-codecheck:
	$(occ) app:check-code $(app_name) -c private -c strong-comparison
	$(occ) app:check-code $(app_name) -c deprecation

.PHONY: test-php-unit
test-php-unit: ## Run php unit tests
test-php-unit:
	# TODO: No tests in here.

.PHONY: test-php-unit-dbg
test-php-unit-dbg: ## Run php unit tests using phpdbg
test-php-unit-dbg:
	# TODO: No tests in here.

.PHONY: test-php-style
test-php-style: ## Run php-cs-fixer and check owncloud code-style
test-php-style: vendor-bin/owncloud-codestyle/vendor
	$(PHP_CS_FIXER) fix -v --diff --diff-format udiff --allow-risky yes --dry-run

.PHONY: test-php-style-fix
test-php-style-fix: ## Run php-cs-fixer and fix code style issues
test-php-style-fix: vendor-bin/owncloud-codestyle/vendor
	$(PHP_CS_FIXER) fix -v --diff --diff-format udiff --allow-risky yes

.PHONY: test-php-phan
test-php-phan: ## Run phan
test-php-phan: vendor-bin/phan/vendor
	$(PHAN) --config-file .phan/config.php --require-config-exists

.PHONY: test-php-phpstan
test-php-phpstan: ## Run phpstan
test-php-phpstan: vendor-bin/phpstan/vendor
	$(PHPSTAN) analyse --memory-limit=4G --configuration=./phpstan.neon --no-progress --level=5 appinfo lib

.PHONY: test-acceptance-api
test-acceptance-api: ## Run API acceptance tests
test-acceptance-api: $(acceptance_test_deps)
	BEHAT_BIN=$(BEHAT_BIN) $(ACCEPTANCE_RUNNER) --remote --type api

.PHONY: test-acceptance-cli
test-acceptance-cli: ## Run CLI acceptance tests
test-acceptance-cli: $(acceptance_test_deps)
	BEHAT_BIN=$(BEHAT_BIN) $(ACCEPTANCE_RUNNER) --remote --type cli

.PHONY: test-acceptance-webui
test-acceptance-webui: ## Run webUI acceptance tests
test-acceptance-webui: $(acceptance_test_deps)
	BEHAT_BIN=$(BEHAT_BIN) $(ACCEPTANCE_RUNNER) --remote --type webUI

#
# Dependency management
#--------------------------------------

composer.lock: composer.json
	@echo composer.lock is not up to date.

vendor: composer.lock
	$(COMPOSER_BIN) install --no-dev

vendor/bamarni/composer-bin-plugin: composer.lock
	$(COMPOSER_BIN) install

vendor-bin/owncloud-codestyle/vendor: vendor/bamarni/composer-bin-plugin vendor-bin/owncloud-codestyle/composer.lock
	$(COMPOSER_BIN) bin owncloud-codestyle install --no-progress

vendor-bin/owncloud-codestyle/composer.lock: vendor-bin/owncloud-codestyle/composer.json
	@echo owncloud-codestyle composer.lock is not up to date.

vendor-bin/phan/vendor: vendor/bamarni/composer-bin-plugin vendor-bin/phan/composer.lock
	$(COMPOSER_BIN) bin phan install --no-progress

vendor-bin/phan/composer.lock: vendor-bin/phan/composer.json
	@echo phan composer.lock is not up to date.

vendor-bin/phpstan/vendor: vendor/bamarni/composer-bin-plugin vendor-bin/phpstan/composer.lock
	$(COMPOSER_BIN) bin phpstan install --no-progress

vendor-bin/phpstan/composer.lock: vendor-bin/phpstan/composer.json
	@echo phpstan composer.lock is not up to date.

vendor-bin/behat/vendor: vendor/bamarni/composer-bin-plugin vendor-bin/behat/composer.lock
	composer bin behat install --no-progress

vendor-bin/behat/composer.lock: vendor-bin/behat/composer.json
	@echo behat composer.lock is not up to date.

vendor-bin/php-scoper/vendor: vendor/bamarni/composer-bin-plugin vendor-bin/php-scoper/composer.lock
	$(COMPOSER_BIN) bin php-scoper install --no-progress

vendor-bin/php-scoper/composer.lock: vendor-bin/php-scoper/composer.json
	@echo php-scoper composer.lock is not up to date.
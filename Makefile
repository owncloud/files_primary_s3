# This file is licensed under the GNU General Public License v2.0
# # @author Sergio Bertolin <sbertolin@owncloud.com>

SHELL := /bin/bash

COMPOSER_BIN := $(shell command -v composer 2> /dev/null)

NPM := $(shell command -v npm 2> /dev/null)

app_name=$(notdir $(CURDIR))
project_directory=$(CURDIR)/../$(app_name)
build_tools_directory=$(CURDIR)/build/tools
appstore_build_directory=$(CURDIR)/build/artifacts/appstore
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
PHPUNITDBG=phpdbg -qrr -d memory_limit=16G -d zend.enable_gc=0 "$(PWD)/../../lib/composer/bin/phpunit"
PHP_CS_FIXER=php -d zend.enable_gc=0 vendor-bin/owncloud-codestyle/vendor/bin/php-cs-fixer
PHAN=php -d zend.enable_gc=0 vendor-bin/phan/vendor/bin/phan
PHPSTAN=php -d zend.enable_gc=0 vendor-bin/phpstan/vendor/bin/phpstan
BEHAT_BIN=vendor-bin/behat/vendor/bin/behat

# start with displaying help
help: ## Show this help message
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//' | sed -e 's/  */ /' | column -t -s :

.PHONY: all
all: vendor

.PHONY: clean
clean: ## Remove appstore build
	rm -rf ./build/artifacts
	rm -rf ./vendor
	rm -Rf vendor-bin/**/vendor vendor-bin/**/composer.lock

.PHONY: dist
dist: ## Builds the appstore package
	make appstore

# Builds the package for the app store, ignores php and js tests
.PHONY: appstore
appstore: ## Builds the package for app store
appstore: vendor
	rm -rf $(appstore_build_directory)
	mkdir -p $(appstore_package_name)
	cp --parents -r \
	appinfo \
	css \
	js \
	l10n \
	lib \
	vendor \
	templates \
	LICENSE \
	CHANGELOG.md \
	$(appstore_package_name)

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
test-php-unit: ## Run core php unit tests
test-php-unit:
	cd ../../tests && $(PHPUNIT) --configuration ./phpunit-autotest.xml

.PHONY: test-php-unit-dbg
test-php-unit-dbg: ## Run core php unit tests using phpdbg and record coverage
test-php-unit-dbg:
	cd ../../tests && $(PHPUNITDBG) --configuration ./phpunit-autotest.xml --coverage-clover ../apps/files_primary_s3/tests/output/clover.xml

.PHONY: test-php-style
test-php-style: ## Run php-cs-fixer and check owncloud code-style
test-php-style: vendor-bin/owncloud-codestyle/vendor
	$(PHP_CS_FIXER) fix -v --diff --allow-risky yes --dry-run

.PHONY: test-php-style-fix
test-php-style-fix: ## Run php-cs-fixer and fix code style issues
test-php-style-fix: vendor-bin/owncloud-codestyle/vendor
	$(PHP_CS_FIXER) fix -v --diff --allow-risky yes

.PHONY: test-php-phan
test-php-phan: ## Run phan
test-php-phan: vendor-bin/phan/vendor
	$(PHAN) --config-file .phan/config.php --require-config-exists

.PHONY: test-php-phpstan
test-php-phpstan: ## Run phpstan
test-php-phpstan: vendor-bin/phpstan/vendor
	$(PHPSTAN) analyse --memory-limit=4G --configuration=./phpstan.neon --no-progress --level=5 appinfo lib

.PHONY: test-acceptance-core-api
test-acceptance-core-api: ## Run core API acceptance tests
test-acceptance-core-api: $(acceptance_test_deps)
	BEHAT_BIN=$(BEHAT_BIN) ../../tests/acceptance/run.sh --remote --type api -c ../../tests/acceptance/config/behat.yml --tags '~@app-required&&~@skipOnStorage:${S3_TYPE}&&~@skip&&${BEHAT_FILTER_TAGS}'

.PHONY: test-acceptance-core-cli
test-acceptance-core-cli: ## Run core CLI acceptance tests
test-acceptance-core-cli: $(acceptance_test_deps)
	BEHAT_BIN=$(BEHAT_BIN) ../../tests/acceptance/run.sh --remote --type cli -c ../../tests/acceptance/config/behat.yml --tags '~@app-required&&~@skipOnStorage:${S3_TYPE}&&~@skip&&${BEHAT_FILTER_TAGS}'

.PHONY: test-acceptance-core-webui
test-acceptance-core-webui: ## Run core webUI acceptance tests
test-acceptance-core-webui: $(acceptance_test_deps)
	BEHAT_BIN=$(BEHAT_BIN) ../../tests/acceptance/run.sh --remote --type webUI -c ../../tests/acceptance/config/behat.yml --tags '~@app-required&&~@skipOnStorage:${S3_TYPE}&&~@skip&&${BEHAT_FILTER_TAGS}'

.PHONY: test-acceptance-api
test-acceptance-api: ## Run API acceptance tests
test-acceptance-api: $(acceptance_test_deps)
	BEHAT_BIN=$(BEHAT_BIN) ../../tests/acceptance/run.sh --remote --type api

.PHONY: test-acceptance-webui
test-acceptance-webui: ## Run webUI acceptance tests
test-acceptance-webui: $(acceptance_test_deps)
	BEHAT_BIN=$(BEHAT_BIN) ../../tests/acceptance/run.sh --remote --type webUI

#
# Dependency management
#--------------------------------------

composer.lock: composer.json
	@echo composer.lock is not up to date.

vendor: composer.lock
ifndef COMPOSER_BIN
	$(error composer is not available on your system, please install composer)
endif
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

#
# Translation
#--------------------------------------

.PHONY: l10n-push
l10n-push:
	cd l10n && tx push -s --skip

.PHONY: l10n-pull
l10n-pull:
	cd l10n && tx pull -a --skip --minimum-perc=75

.PHONY: l10n-clean
l10n-clean:
	rm -rf l10n/l10n.pl
	find l10n -type f -name \*.po -or -name \*.pot | xargs rm -f
	find l10n -type f -name uz.\* -or -name yo.\* -or -name ne.\* -or -name or_IN.\* | xargs git rm -f || true

.PHONY: l10n-read
l10n-read: l10n/l10n.pl
	cd l10n && perl l10n.pl $(app_name) read

.PHONY: l10n-write
l10n-write: l10n/l10n.pl
	cd l10n && perl l10n.pl $(app_name) write

l10n/l10n.pl:
	wget -qO l10n/l10n.pl https://raw.githubusercontent.com/owncloud-ci/transifex/d1c63674d791fe8812216b29da9d8f2f26e7e138/rootfs/usr/bin/l10n

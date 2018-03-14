# Deps

COMPOSER=$(tools_path)/composer.phar

composer_deps=lib/composer
composer_dev_deps=lib/composer/phpunit
clean_rules+=clean-deps
help_rules+=help-deps

.PHONY: help-deps
help-deps:
	@echo -e "Dependencies:\n"
	@echo -e "deps\t\tto fetch all dependencies"
	@echo -e "update-composer\tto update composer.lock"
	@echo

.PHONY: clean-deps
clean-deps: clean-composer
	rm -Rf $(tools_path)/*.phar

.PHONY: clean-composer
clean-composer:
	rm -Rf $(composer_deps)/

$(COMPOSER):
	cd "$(tools_path)" && curl -ss https://getcomposer.org/installer | php
	chmod u+x $@

$(composer_deps): $(COMPOSER) composer.json composer.lock
	php $(COMPOSER) install --no-dev && touch $@

$(composer_dev_deps): $(COMPOSER) composer.json composer.lock
	php $(COMPOSER) install -n --no-progress

.PHONY: update-composer
update-composer:
	php $(COMPOSER) update

.PHONY: deps
deps: $(composer_deps)

.PHONY: dev-deps
dev-deps: $(composer_dev_deps)


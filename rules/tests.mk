# Tests

PHPUNIT="$(shell pwd)"/lib/composer/phpunit/phpunit/phpunit
OCULAR=$(shell pwd)/lib/composer/scrutinizer/ocular/bin/ocular

test_rules+=test-codecheck test-codecheck-deprecations test-php
clean_rules+=clean-deps
help_rules+=help-test

tests_unit_results=tests/unit/results
clover_xml=$(tests_unit_results)/clover.xml
phpunit_args=--log-junit $(tests_unit_results)/results.xml
ifndef NOCOVERAGE # env variable
phpunit_args+=--coverage-clover $(clover_xml) --coverage-html $(tests_unit_results)/coverage-html
endif

.PHONY: help-test
help-test:
	@echo -e "Testing:\n"
	@echo -e "test\t\t\tto run all test suites"
	@echo -e "test-syntax\t\tto run syntax checks"
	@echo -e "test-codecheck\t\tto run the code checker"
	@echo -e "test-php\t\tto run PHP test suites"
	@echo -e "test-acceptance\tto run acceptance tests"
	@echo

.PHONY: clean-test
clean-test:
	rm -Rf $(tests_unit_results)

.PHONY: test-syntax
test-syntax: test-syntax-php test-syntax-js

.PHONY: test-syntax-php
test-syntax-php:
	for F in $(shell find . -name \*.php | grep -v -e 'lib/composer' -e 'vendor'); do \
		php -l "$$F" > /dev/null || exit $?; \
	done

.PHONY: test-codecheck
test-codecheck: test-syntax-php
	$(OCC) app:check-code $(app_name) -c private -c strong-comparison

.PHONY: test-codecheck-deprecations
test-codecheck-deprecations:
	$(OCC) app:check-code $(app_name) -c deprecation

.PHONY: test-php
test-php: $(PHPUNIT) test-syntax-php
	$(OCC) app:enable $(app_name)
	$(PHPUNIT) --configuration tests/unit/phpunit.xml $(phpunit_args)

$(clover_xml): test-php

.PHONY: test-upload-coverage
test-upload-coverage: $(OCULAR) $(clover_xml)
	$(OCULAR) code-coverage:upload --format=php-clover $(clover_xml)

#.PHONY: test-acceptance
#test-acceptance: test-syntax-php
#	cd tests/acceptance && OCC="$(OCC)" ./run.sh


$(PHPUNIT): $(composer_dev_deps)
$(OCULAR): $(composer_dev_deps)



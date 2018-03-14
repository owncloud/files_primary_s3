# Makefile

OWNCLOUD_PATH=$(CURDIR)/../..
OCC=$(OWNCLOUD_PATH)/occ

app_name=files_primary_s3
app_namespace=Files_Primary_S3

build_dir=$(CURDIR)/build

# these can be extended by included files
# to add for example generated files
doc_files=README.md CHANGELOG.md
src_dirs=appinfo lib 
all_src=$(src_files) $(src_dirs) $(doc_files)

# rules to be extended by included files
build_rules=
test_rules=
clean_rules=
help_rules=help-base

tools_path=$(shell pwd)/tools

.PHONY: all
all: help-hint dist

include rules/deps.mk
include rules/sign.mk
include rules/dist.mk
include rules/tests.mk

.PHONY: help-base
help-base:
	@echo "Please use 'make <target>' where <target> is one of"
	@echo

.PHONY: help-hint
help-hint:
	@echo "Building $(app_name) app"
	@echo
	@echo "Note: You can type 'make help' for more targets"
	@echo

.PHONY: help
help: $(help_rules)

.PHONY: clean
clean: $(clean_rules)

.PHONY: test
test: $(test_rules)


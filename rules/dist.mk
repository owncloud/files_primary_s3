# Deps
# Build and dist rules

help_rules+=help-dist
clean_rules+=clean-build

.PHONY: help-dist
help-dist:
	@echo -e "Building:\n"
	@echo -e "dist\t\tto build the distribution folder and tarball $(app_name).tar.gz"
	@echo -e "clean\t\tto clean everything"
	@echo

.PHONY: clean-build
clean-build:
	rm -Rf $(build_dir)

.PHONY: dist
dist: $(build_dir)/$(app_name).tar.gz

$(build_dir)/$(app_name).tar.gz: $(build_dir)/$(app_name) $(signature_file)
	cd $(build_dir); tar czf $@ $(app_name)
	@echo Tarball was built in $@

$(build_dir)/$(app_name).tar.bz2: $(build_dir)/$(app_name) $(signature_file)
	cd $(build_dir); tar cjf $@ $(app_name)
	@echo Tarball was built in $@

$(build_dir)/$(app_name): deps $(build_rules) $(all_src)
	mkdir -p $@
	cp -R $(all_src) $@
	@echo Removing unwanted files...
	find $@ \( \
		-name .gitkeep -o \
		-name .gitignore -o \
		-name no-php \
		\) -print | xargs rm -Rf
	find $@/{lib/composer/vendor/} \( \
		-name bin -o \
		-name test -o \
		-name tests -o \
		-name examples -o \
		-name demo -o \
		-name demos -o \
		-name doc -o \
		-name travis -o \
		-name testem.json -o \
		-iname \*.sh -o \
		-iname \*.exe \
		\) -print | xargs rm -Rf
	touch $@

.PHONY: distclean
distclean: clean-build

.PHONY: clean-dist
clean-dist: clean-build


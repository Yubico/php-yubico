VERSION=2.4
PACKAGE=Auth_Yubico
CODE=COPYING NEWS README Yubico.php package.xml demo.php
EXAMPLE=example/admin.php example/authenticate.php example/bg.jpg	\
	example/config.php example/db.sql example/debug.php		\
	example/greenBG.jpg example/greenGraphic.jpg			\
	example/img_press.jpg example/index.html example/logo.jpg	\
	example/Modhex_Calculator.php example/Modhex.php		\
	example/one_factor.php example/style.css			\
	example/two_factor_legacy.php example/two_factor.php		\
	example/yubicoLogo.jpg						\
	example/yubikey.jpg example/yubiright_16x16.gif

all: sync-version $(PACKAGE)-$(VERSION).tgz

$(PACKAGE)-$(VERSION).tgz: $(CODE) $(EXAMPLE)
	mkdir $(PACKAGE)-$(VERSION) $(PACKAGE)-$(VERSION)/example
	cp $(CODE) $(PACKAGE)-$(VERSION)
	cp $(EXAMPLE) $(PACKAGE)-$(VERSION)/example
	tar cfz $(PACKAGE)-$(VERSION).tgz $(PACKAGE)-$(VERSION)
	rm -rf $(PACKAGE)-$(VERSION)

.PHONY: sync-version
sync-version:
	cat package.xml | perl -p -e "s,<release>[0-9.]+</release>,<release>"$(VERSION)"</release>," > tmp && \
	if ! cmp package.xml tmp; then \
		mv tmp package.xml; \
	else \
		rm tmp; \
	fi
	cat README | perl -p -e "s,Auth_Yubico-[0-9.]+.tgz,Auth_Yubico-"$(VERSION)".tgz," > tmp && \
	if ! cmp README tmp; then \
		mv tmp README; \
	else \
		rm tmp; \
	fi

clean:
	rm -f *~
	rm -rf $(PACKAGE)-$(VERSION)

PROJECT=php-yubico

release:
	@if test -z "$(USER)" || test -z "$(KEYID)"; then \
		echo "Try this instead:"; \
		echo "  make release USER=[GOOGLEUSERNAME] KEYID=[PGPKEYID]"; \
		echo "For example:"; \
		echo "  make release USER=simon@yubico.com KEYID=2117364A"; \
		exit 1; \
	fi
	make
	gpg --detach-sign --default-key $(KEYID) $(PACKAGE)-$(VERSION).tgz
	gpg --verify $(PACKAGE)-$(VERSION).tgz.sig
	git push
	git tag -u $(KEYID)! -m $(VERSION) $(PACKAGE)-$(VERSION)
	git push --tags
	mkdir -p ../releases/$(PACKAGE)/ && \
		cp -v $(PACKAGE)-$(VERSION).tgz* ../releases/$(PACKAGE)/
	googlecode_upload.py -s "OpenPGP signature for $(PACKAGE) $(VERSION)." \
	 -p $(PROJECT) -u $(USER) $(PACKAGE)-$(VERSION).tgz.sig
	googlecode_upload.py -s "Auth_Yubico $(VERSION)." \
	 -p $(PROJECT) -u $(USER) $(PACKAGE)-$(VERSION).tgz 
	cp README ../wiki-$(PROJECT)/ReadMe.wiki && \
		cd ../wiki-$(PROJECT) && \
		svn commit -m Sync. ReadMe.wiki

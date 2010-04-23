VERSION=2.0
PACKAGE=Auth_Yubico
CODE=Yubico.php package.xml README demo.php
EXAMPLE=example/admin.php example/authenticate.php example/bg.jpg	\
	example/config.php example/db.sql example/debug.php		\
	example/greenBG.jpg example/greenGraphic.jpg			\
	example/img_press.jpg example/index.html example/logo.jpg	\
	example/Modhex_Calculator.php example/Modhex.php		\
	example/one_factor.php example/style.css			\
	example/two_factor_legacy.php example/two_factor.php		\
	example/yubicoLogo.gif example/yubicoLogo.jpg			\
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
USER=simon75j
KEYID=B9156397

release:
	make
	gpg --detach-sign --default-key $(KEYID) $(PACKAGE)-$(VERSION).tgz
	gpg --verify $(PACKAGE)-$(VERSION).tgz.sig
	svn copy https://$(PROJECT).googlecode.com/svn/trunk/ \
	 https://$(PROJECT).googlecode.com/svn/tags/$(PACKAGE)-$(VERSION) \
	 -m "Tagging the $(VERSION) release of the $(PACKAGE) project."
	googlecode_upload.py -s "OpenPGP signature for $(PACKAGE) $(VERSION)." \
	 -p $(PROJECT) -u $(USER) $(PACKAGE)-$(VERSION).tgz.sig
	googlecode_upload.py -s "Auth_Yubico $(VERSION)." \
	 -p $(PROJECT) -u $(USER) $(PACKAGE)-$(VERSION).tgz 
	cp README ../wiki-$(PROJECT)/ReadMe.wiki && \
		cd ../wiki-$(PROJECT) && \
		svn commit -m Sync. ReadMe.wiki

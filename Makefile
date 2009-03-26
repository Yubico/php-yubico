# Copyright (c) 2007, 2008, 2009  Simon Josefsson.  All rights reserved.
# 
# Redistribution and use in source and binary forms, with or without
# modification, are permitted provided that the following conditions
# are met:
# 
# o Redistributions of source code must retain the above copyright
#   notice, this list of conditions and the following disclaimer.
# o Redistributions in binary form must reproduce the above copyright
#   notice, this list of conditions and the following disclaimer in the
#   documentation and/or other materials provided with the distribution.
# o The names of the authors may not be used to endorse or promote
#   products derived from this software without specific prior written
#   permission.
# 
# THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
# "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
# LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
# A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
# OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
# SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
# LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
# DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
# THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
# (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
# OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

VERSION=1.8
PACKAGE=Auth_Yubico
FILES=Yubico.php package.xml README demo.php example/admin.php		\
	example/authenticate.php example/config.php example/db.sql	\
	example/debug.php example/img_press.jpg example/index.html	\
	example/logo.jpg example/one_factor.php example/style.css	\
	example/two_factor_legacy.php example/two_factor.php

all: sync-version $(PACKAGE)-$(VERSION).tgz

$(PACKAGE)-$(VERSION).tgz: $(FILES)
	mkdir $(PACKAGE)-$(VERSION)
	cp $(FILES) $(PACKAGE)-$(VERSION)
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
	make
	gpg -b $(PACKAGE)-$(VERSION).tgz
	gpg --verify $(PACKAGE)-$(VERSION).tgz.sig
	svn copy https://$(PROJECT).googlecode.com/svn/trunk/ \
	 https://$(PROJECT).googlecode.com/svn/tags/$(PACKAGE)-$(VERSION) \
	 -m "Tagging the $(VERSION) release of the $(PACKAGE) project."
	googlecode_upload.py -s "OpenPGP signature for $(PACKAGE) $(VERSION)." \
	 -p $(PROJECT) -u simon75j $(PACKAGE)-$(VERSION).tgz.sig
	googlecode_upload.py -s "Auth_Yubico $(VERSION)." \
	 -p $(PROJECT) -u simon75j $(PACKAGE)-$(VERSION).tgz 
	cp README ../wiki-$(PROJECT)/ReadMe.wiki && \
		cd ../wiki-$(PROJECT) && \
		svn commit -m Sync. ReadMe.wiki

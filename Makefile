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

VERSION=1.4
PACKAGE=Auth_Yubico
FILES=Yubico.php package.xml README

all: $(PACKAGE)-$(VERSION).tgz

$(PACKAGE)-$(VERSION).tgz: $(FILES)
	mkdir $(PACKAGE)-$(VERSION)
	cp $(FILES) $(PACKAGE)-$(VERSION)
	tar cfz $(PACKAGE)-$(VERSION).tgz $(PACKAGE)-$(VERSION)
	rm -rf $(PACKAGE)-$(VERSION)

clean:
	rm -f *~
	rm -rf $(PACKAGE)-$(VERSION)

release:
	make
	gpg -b $(PACKAGE)-$(VERSION).tgz
	gpg --verify $(PACKAGE)-$(VERSION).tgz.sig
	svn copy https://php-yubico.googlecode.com/svn/trunk/ \
	 https://php-yubico.googlecode.com/svn/tags/$(PACKAGE)-$(VERSION) \
	 -m "Tagging the $(VERSION) release of the $(PACKAGE) project."
	googlecode_upload.py -s "Auth_Yubico $(VERSION)." \
	 -p php-yubico -u simon75j $(PACKAGE)-$(VERSION).tar.gz 

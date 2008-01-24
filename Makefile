VERSION=1.1
PACKAGE=Auth_Yubico
FILES=Yubico.php package.xml yubico-php.pdf yubico-php.html

all: $(PACKAGE)-$(VERSION).tgz

$(PACKAGE)-$(VERSION).tgz: $(FILES)
	mkdir $(PACKAGE)-$(VERSION)
	cp $(FILES) $(PACKAGE)-$(VERSION)
	tar cfz $(PACKAGE)-$(VERSION).tgz $(PACKAGE)-$(VERSION)
	rm -rf $(PACKAGE)-$(VERSION)

yubico-php.pdf yubico-php.html: README
	cp README yubico-php.txt
	a2x yubico-php.txt
	docbook2pdf yubico-php.xml
	rm -f yubico-php.txt yubico-php.xml

clean:
	rm -f *~
	rm -rf $(PACKAGE)-$(VERSION)

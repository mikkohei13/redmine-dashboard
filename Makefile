#!make

#PWD=$(shell pwd)
HTMLDIR=html
DATADIR=data
ERRORSDIR=errors

all:
.PHONY: all

setup:
	test -d $(HTMLDIR) || mkdir $(HTMLDIR)
	test -d $(DATADIR) || mkdir $(DATADIR)
	chmod a+w $(DATADIR)
	test -d $(ERRORSDIR) || mkdir $(ERRORSDIR)
	chmod a+w $(ERRORSDIR)

clean:
	docker-compose down -v
	rm -fr $(DATADIR)
	rm -fr $(ERRORSDIR)
	

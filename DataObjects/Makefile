PEAR   = `pwd`/../pear
CREATE =  $(PEAR)/DB/DataObject/createTables.php 

all: Touristmain.ini 

Touristmain.ini: config.aspen.ini
	php4 -d include_path="$(PEAR):." $(CREATE) `pwd`/$<
	cp Touristmain.ini Touristtest.ini 
	cp Touristmain.links.ini Touristtest.links.ini 

clean:
	rm -f Touristtest.ini Touristmain.ini

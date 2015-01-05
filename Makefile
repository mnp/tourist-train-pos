
all: doBuild doDataObjects

doBuild:
	$(MAKE) -C build clean
	$(MAKE) -C build all

doDataObjects:
	$(MAKE) -C DataObjects clean
	$(MAKE) -C DataObjects

upload:
	@rev="`svn info | awk -F/ '/^URL:/ {print $NF}'`"; \
	if [ "$rev" != Touristtrain ]; then       \
	  echo must upload from stable branch;   \
	  exit;					\
	fi;					\
	if [ 0 -eq `svn diff | wc -l` ]; then	\
	  echo 'No changes, making version';	\
	  echo '<? $$version =' `svn info | awk '/^Revision:/{print $$2}'` '; ?>' > version.php; \
	  svn ci -m version version.php;	\
	  echo 'uploading';			\
	  sitecopy -k -u train;			\
	else  					\
	  echo 'Changes pending, will not upload'; \
	fi

tags:
	-rm -f TAGS
	etags -r '/[ \t]*function[ \t\n]+[^ \t\n(]+/' \
	      -r '/[ \t]*class[ \t\n]+[^ \t\n]+/' \
	      -r '/[ \t]*define\(\"[^\"]+\)/' \
	      *.php \
	      lib/*.php \
	      src/*.php \
	      DataObjects/*.php 


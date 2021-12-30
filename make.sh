SRC=/home/htf/soft/php-8.1.1
OPTIONS="--disable-all \
--with-openssl=/usr/openssl \
--with-openssl-dir=/usr/openssl \
--with-curl=/usr/curl \
--enable-swoole --enable-sockets --enable-mysqlnd --enable-http2 --enable-swoole-json --enable-swoole-curl --enable-cares \
--enable-posix \
--enable-sockets \
--enable-pcntl \
--enable-bcmath \
--with-bz2 \
--enable-pdo \
--with-mysqli \
--with-pdo_mysql \
--enable-mysqlnd \
--with-iconv \
--enable-fileinfo \
--enable-intl \
--enable-filter \
--enable-session \
--enable-tokenizer \
--enable-mbstring \
--enable-opcache \
--enable-ctype \
--with-zlib \
--with-zip \
--enable-phar \
--with-sqlite3 \
--with-pdo-sqlite"

if [ "$1" = "docker-build" ] ;then
  sudo docker build . -t phpswoole/swoole_cli_os:latest \
     --build-arg OPENSSL_FILE=openssl-1.1.1k.tar.gz \
     --build-arg CURL_FILE=curl-curl-7_77_0.tar.gz \
     --build-arg LIBICONV_FILE=libiconv-1.16.tar.gz
elif [ "$1" = "config" ] ;then
   echo $OPTIONS
  ./configure $OPTIONS
elif [ "$1" = "static-config" ] ;then
   rm ./configure
   ./buildconf --force
   mv main/php_config.h.in /tmp/cnt
   echo -ne '#ifndef __PHP_CONFIG_H\n#define __PHP_CONFIG_H\n' > main/php_config.h.in
   cat /tmp/cnt >> main/php_config.h.in
   echo -ne '\n#endif\n' >> main/php_config.h.in
   echo $OPTIONS
   export PKG_CONFIG_PATH=/usr/openssl/lib/pkgconfig:/usr/curl/lib/pkgconfig:$PKG_CONFIG_PATH
  ./configure $OPTIONS
elif [ "$1" = "diff-configure" ] ;then
  meld $SRC/configure.ac ./configure.ac
elif [ "$1" = "sync" ] ;then
  # ZendVM
  cp -r $SRC/Zend ./
  # Extension
  cp -r $SRC/ext/bcmath/ ./ext
  cp -r $SRC/ext/bz2/ ./ext
  cp -r $SRC/ext/calendar/ ./ext
  cp -r $SRC/ext/ctype/ ./ext
  cp -r $SRC/ext/curl/ ./ext
  cp -r $SRC/ext/date/ ./ext
  cp -r $SRC/ext/dom/ ./ext
  cp -r $SRC/ext/exif/ ./ext
  cp -r $SRC/ext/fileinfo/ ./ext
  cp -r $SRC/ext/filter/ ./ext
  cp -r $SRC/ext/gd/ ./ext
  cp -r $SRC/ext/gettext/ ./ext
  cp -r $SRC/ext/gmp/ ./ext
  cp -r $SRC/ext/hash/ ./ext
  cp -r $SRC/ext/iconv/ ./ext
  cp -r $SRC/ext/intl/ ./ext
  cp -r $SRC/ext/json/ ./ext
  cp -r $SRC/ext/libxml/ ./ext
  cp -r $SRC/ext/mbstring/ ./ext
  cp -r $SRC/ext/mysqli/ ./ext
  cp -r $SRC/ext/mysqlnd/ ./ext
  cp -r $SRC/ext/opcache/ ./ext
  cp -r $SRC/ext/openssl/ ./ext
  cp -r $SRC/ext/pcntl/ ./ext
  cp -r $SRC/ext/pcre/ ./ext
  cp -r $SRC/ext/pdo/ ./ext
  cp -r $SRC/ext/pdo_mysql/ ./ext
  cp -r $SRC/ext/pdo_sqlite/ ./ext
  cp -r $SRC/ext/phar/ ./ext
  cp -r $SRC/ext/posix/ ./ext
  cp -r $SRC/ext/readline/ ./ext
  cp -r $SRC/ext/reflection/ ./ext
  cp -r $SRC/ext/session/ ./ext
  cp -r $SRC/ext/simplexml/ ./ext
  cp -r $SRC/ext/soap/ ./ext
  cp -r $SRC/ext/sockets/ ./ext
  cp -r $SRC/ext/sodium/ ./ext
  cp -r $SRC/ext/spl/ ./ext
  cp -r $SRC/ext/sqlite3/ ./ext
  cp -r $SRC/ext/standard/ ./ext
  cp -r $SRC/ext/sysvshm/ ./ext
  cp -r $SRC/ext/tokenizer/ ./ext
  cp -r $SRC/ext/xml/ ./ext
  cp -r $SRC/ext/xmlreader/ ./ext
  cp -r $SRC/ext/xmlwriter/ ./ext
  cp -r $SRC/ext/xsl/ ./ext
  cp -r $SRC/ext/zip/ ./ext
  cp -r $SRC/ext/zlib/ ./ext
  # main
  cp -r $SRC/main ./main
  cp -r sapi/cli sapi/cli
  cp -r ./TSRM/TSRM.h main/TSRM.h
  exit 0
fi


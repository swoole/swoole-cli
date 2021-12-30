SRC=/home/htf/soft/php-8.1.1
ROOT=$(pwd)
OPTIONS="--disable-all \
--with-openssl=/usr/openssl --with-openssl-dir=/usr/openssl \
--with-curl=/usr/curl \
--with-iconv=/usr \
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

make_openssl() {
    cd /work/pool/lib
    echo "build openssl"
    mkdir -p /work/pool/lib/openssl && \
    tar --strip-components=1 -C /work/pool/lib/openssl -xf /work/pool/lib/openssl-1.1.1m.tar.gz  && \
    cd openssl && \
    echo  "./config -static --static no-shared --prefix=/usr/openssl"
    ./config -static --static no-shared --prefix=/usr/openssl && \
    make -j 8 && \
    make install
}

make_curl() {
    cd /work/pool/lib
    echo "build curl"
    mkdir -p /work/pool/lib/curl && \
    tar --strip-components=1 -C /work/pool/lib/curl -xf /work/pool/lib/curl-7.80.0.tar.gz  && \
    cd curl && \
    echo  "autoreconf -fi && \ 
./configure --prefix=/usr/curl --enable-static --disable-shared --with-openssl=/usr/openssl"
    autoreconf -fi && \ 
./configure --prefix=/usr/curl --enable-static --disable-shared --with-openssl=/usr/openssl && \
    make -j 8 && \
    make install
}

make_libiconv() {
    cd /work/pool/lib
    echo "build libiconv"
    mkdir -p /work/pool/lib/libiconv && \
    tar --strip-components=1 -C /work/pool/lib/libiconv -xf /work/pool/lib/libiconv-1.16.tar.gz  && \
    cd libiconv && \
    echo  "./configure --prefix=/usr enable_static=yes enable_shared=no"
    ./configure --prefix=/usr enable_static=yes enable_shared=no && \
    make -j 8 && \
    make install
}

make_sqlite3() {
    cd /work/pool/lib
    echo "build sqlite3"
    mkdir -p /work/pool/lib/sqlite3 && \
    tar --strip-components=1 -C /work/pool/lib/sqlite3 -xf /work/pool/lib/sqlite-autoconf-3370000.tar.gz  && \
    cd sqlite3 && \
    echo  "./configure --prefix=/usr --enable-static --disable-shared"
    ./configure --prefix=/usr --enable-static --disable-shared && \
    make -j 8 && \
    make install
}

make_zlib() {
    cd /work/pool/lib
    echo "build zlib"
    mkdir -p /work/pool/lib/zlib && \
    tar --strip-components=1 -C /work/pool/lib/zlib -xf /work/pool/lib/zlib-1.2.11.tar.gz  && \
    cd zlib && \
    echo  "./configure --prefix=/usr --static"
    ./configure --prefix=/usr --static && \
    make -j 8 && \
    make install
}

make_bzip2() {
    cd /work/pool/lib
    echo "build bzip2"
    mkdir -p /work/pool/lib/bzip2 && \
    tar --strip-components=1 -C /work/pool/lib/bzip2 -xf /work/pool/lib/bzip2-1.0.8.tar.gz  && \
    cd bzip2 && \
    echo  "echo make libbzip2.a"
    echo make libbzip2.a && \
    make -j 8 && \
    make install
}

make_icu() {
    cd /work/pool/lib
    echo "build icu"
    mkdir -p /work/pool/lib/icu && \
    tar --strip-components=1 -C /work/pool/lib/icu -xf /work/pool/lib/icu4c-60_3-src.tgz  && \
    cd icu && \
    echo  "source/runConfigureICU Linux --enable-static --disable-shared"
    source/runConfigureICU Linux --enable-static --disable-shared && \
    make -j 8 && \
    make install
}

make_oniguruma() {
    cd /work/pool/lib
    echo "build oniguruma"
    mkdir -p /work/pool/lib/oniguruma && \
    tar --strip-components=1 -C /work/pool/lib/oniguruma -xf /work/pool/lib/oniguruma-6.9.7.tar.gz  && \
    cd oniguruma && \
    echo  "./autogen.sh && ./configure --prefix=/usr --enable-static --disable-shared"
    ./autogen.sh && ./configure --prefix=/usr --enable-static --disable-shared && \
    make -j 8 && \
    make install
}

make_zip() {
    cd /work/pool/lib
    echo "build zip"
    mkdir -p /work/pool/lib/zip && \
    tar --strip-components=1 -C /work/pool/lib/zip -xf /work/pool/lib/libzip-1.8.0.tar.gz  && \
    cd zip && \
    echo  "./configure --prefix=/usr --enable-static --disable-shared"
    ./configure --prefix=/usr --enable-static --disable-shared && \
    make -j 8 && \
    make install
}


make_all_library() {
    make_openssl && echo "[SUCCESS] make openssl"
    make_curl && echo "[SUCCESS] make curl"
    make_libiconv && echo "[SUCCESS] make libiconv"
    make_sqlite3 && echo "[SUCCESS] make sqlite3"
    make_zlib && echo "[SUCCESS] make zlib"
    make_bzip2 && echo "[SUCCESS] make bzip2"
    make_icu && echo "[SUCCESS] make icu"
    make_oniguruma && echo "[SUCCESS] make oniguruma"
    make_zip && echo "[SUCCESS] make zip"
}

if [ "$1" = "docker-build" ] ;then
  sudo docker build -t phpswoole/swoole_cli_os:latest .
elif [ "$1" = "docker-bash" ] ;then
  sudo docker run -it -v $ROOT:/work -v /home/htf/workspace/swoole:/work/ext/swoole phpswoole/swoole_cli_os /bin/bash
elif [ "$1" = "config" ] ;then
   echo $OPTIONS
  ./configure $OPTIONS
elif [ "$1" = "all-library" ] ;then
    make_all_library
elif [ "$1" = "openssl" ] ;then
    make_openssl && echo "[SUCCESS] make openssl"
elif [ "$1" = "curl" ] ;then
    make_curl && echo "[SUCCESS] make curl"
elif [ "$1" = "libiconv" ] ;then
    make_libiconv && echo "[SUCCESS] make libiconv"
elif [ "$1" = "sqlite3" ] ;then
    make_sqlite3 && echo "[SUCCESS] make sqlite3"
elif [ "$1" = "zlib" ] ;then
    make_zlib && echo "[SUCCESS] make zlib"
elif [ "$1" = "bzip2" ] ;then
    make_bzip2 && echo "[SUCCESS] make bzip2"
elif [ "$1" = "icu" ] ;then
    make_icu && echo "[SUCCESS] make icu"
elif [ "$1" = "oniguruma" ] ;then
    make_oniguruma && echo "[SUCCESS] make oniguruma"
elif [ "$1" = "zip" ] ;then
    make_zip && echo "[SUCCESS] make zip"
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


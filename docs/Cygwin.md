工具
----
- autoconf
- automake
- libtool
- bison
- wget
- tar
- gcc-g++
- openssl
- re2c （需要源码安装）

库
----
```
libssl-devel
libcurl-devel
libxml2-devel
libxslt-devel
libgmp-devel
ImageMagick
libpng-devel
libjpeg-devel
libfreetype-devel
libwebp-devel
libsqlite3-devel
zlib-devel
libbz2-devel
libzip-devel
libicu-devel
libonig-devel
libcares-devel
libsodium-devel
libyaml-devel
libMagick-devel
libzstd-devel
libbrotli-devel
```

编译参数
------
```
./configure --prefix=/usr --disable-all \
--disable-fiber-asm \
--disable-opcache \
--without-pcre-jit \
--with-openssl --enable-openssl \
--with-curl \
--with-iconv \
--enable-intl \
--with-bz2 \
--enable-bcmath \
--enable-filter \
--enable-session \
--enable-tokenizer \
--enable-mbstring \
--enable-ctype \
--with-zlib \
--with-zip \
--enable-posix \
--enable-sockets \
--enable-pdo \
--with-sqlite3 \
--enable-phar \
--enable-pcntl \
--enable-mysqlnd \
--with-mysqli \
--enable-fileinfo \
--with-pdo_mysql \
--with-pdo-sqlite \
--enable-soap \
--with-xsl \
--with-gmp \
--enable-exif \
--with-sodium \
--enable-xml --enable-simplexml --enable-xmlreader --enable-xmlwriter --enable-dom --with-libxml \
--enable-gd --with-jpeg  --with-freetype \
--enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares \
--enable-redis \
--with-imagick \
--with-yaml \
--enable-mongodb --with-mongodb-client-side-encryption=no
```

工具
----
autoconf
automake
g++
cmake
libtool

re2c: 需要源码安装
bison: 3.x 以上版本，macOS 下需要编译安装

问题记录
======

CygWin
----
cygwin下intl 扩展有问题，需要增加 `-D_POSIX_C_SOURCE=200809L` 编译参数，暂时未解决

macOS
----
缺少`libtool`:
```shell
ln -s /usr/local/bin/glibtoolize /usr/local/bin/libtoolize
```


posix 问题

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
libzip2-devel
libicu-devel
libonig-devel
libcares-devel
libsodium-devel
libyaml-devel
libMagick-devel
```


编译参数
------
```
--enable-intl
```

```
./configure --prefix=/usr --disable-all \
--disable-fiber-asm \
--disable-opcache \
--without-pcre-jit \
--with-openssl --enable-openssl \
--with-curl \
--with-iconv \
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
--enable-swoole --enable-sockets --enable-mysqlnd --enable-http2 --enable-swoole-json --enable-swoole-curl --enable-cares \
--enable-redis \
--with-imagick \
--with-yaml 
```
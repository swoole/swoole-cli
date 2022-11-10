wget -O redis.tar.gz https://pecl.php.net/get/redis-5.3.7.tgz
wget -O mongodb.tar.gz https://pecl.php.net/get/mongodb-1.14.2.tgz
wget -O yaml.tar.gz https://pecl.php.net/get/yaml-2.2.2.tgz.tgz
wget -O imagick.tar.gz https://pecl.php.net/get/imagick-3.7.0.tgz

tar xvf redis.tgz
tar xvf mongodb.tgz
tar xvf yaml.tgz
tar xvf imagick.tgz

mv redis ext/redis
mv mongodb ext/mongodb
mv yaml ext/yaml
mv imagick ext/imagick

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
    --with-yaml 
    --enable-mongodb

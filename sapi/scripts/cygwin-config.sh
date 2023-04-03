set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../
  pwd
)
cd ${__PROJECT__}
ROOT=${__PROJECT__}


:<<'EOF'

REDIS_VERSION=5.3.7
MONGODB_VERSION=1.14.2
YAML_VERSION=2.2.2
IMAGICK_VERSION=3.7.0

if [ ! -d pool/ext ]; then
    mkdir -p pool/ext
fi

cd pool/ext

if [ ! -d $ROOT/ext/redis ]; then
    if [ ! -f redis-${REDIS_VERSION}.tgz ]; then
        wget https://pecl.php.net/get/redis-${REDIS_VERSION}.tgz
    fi
    tar xvf redis-${REDIS_VERSION}.tgz
    mv redis-${REDIS_VERSION} $ROOT/ext/redis
fi

if [ ! -d $ROOT/ext/mongodb ]; then
    if [ ! -f redis-${MONGODB_VERSION}.tgz ]; then
        wget https://pecl.php.net/get/mongodb-${MONGODB_VERSION}.tgz
    fi
    tar xvf mongodb-${MONGODB_VERSION}.tgz
    mv mongodb-${MONGODB_VERSION} $ROOT/ext/mongodb
fi

if [ ! -d $ROOT/ext/yaml ]; then
    if [ ! -f redis-${YAML_VERSION}.tgz ]; then
        wget https://pecl.php.net/get/yaml-${YAML_VERSION}.tgz
    fi
    tar xvf yaml-${YAML_VERSION}.tgz
    mv yaml-${YAML_VERSION} $ROOT/ext/yaml
fi

if [ ! -d $ROOT/ext/imagick ]; then
    if [ ! -f redis-${IMAGICK_VERSION}.tgz ]; then
        wget https://pecl.php.net/get/imagick-${IMAGICK_VERSION}.tgz
    fi
    tar xvf imagick-${IMAGICK_VERSION}.tgz
    mv imagick-${IMAGICK_VERSION} $ROOT/ext/imagick
fi

EOF

cd $ROOT
ls -lh ext
./buildconf --force
set +eux
# make clean
set -exu
./configure --prefix=/usr --disable-all \
    --enable-shared=no \
    --enable-static=yes \
    --disable-fiber-asm \
    --enable-opcache \
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
    --with-readline


# make -j $(nproc)
# ./bin/swoole-cli -v

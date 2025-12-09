if [ ! -f ./configure ]; then
  ./buildconf --force
fi

./configure --prefix=/usr/local/swoole-cli \
  --disable-all \
  --enable-zts \
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
  --enable-soap \
  --with-xsl \
  --with-gmp \
  --enable-exif \
  --with-sodium \
  --enable-xml --enable-simplexml --enable-xmlreader --enable-xmlwriter --enable-dom --with-libxml \
  --enable-gd --with-jpeg --with-freetype --with-avif \
  --enable-swoole \
  --enable-swoole-curl \
  --enable-cares \
  --enable-swoole-pgsql \
  --enable-swoole-sqlite \
  --with-swoole-odbc=unixODBC,/usr \
  --enable-swoole-thread \
  --enable-swoole-stdext \
  --enable-brotli \
  --enable-zstd \
  --enable-redis \
  --with-imagick \
  --with-yaml \
  --with-readline \
  --enable-phpy --with-python-config=python3-config \
  --enable-opcache

make -j "$(nproc)"

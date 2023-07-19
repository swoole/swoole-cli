#!/bin/bash

set -x
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)

cd ${__DIR__}

LIBRARIES=$(
cat <<-EOF
openssl
cares
libiconv
libxml2
bzip2
zlib
brotli
liblz4
liblzma
libzstd
nghttp2
nghttp3
ngtcp2
libssh2
curl
oniguruma
libzip
sqlite3
icu
libxslt
gmp
libsodium
ncurses
readline
libjpeg
libpng
freetype
libgif
libwebp
pgsql
libyaml
imagemagick
EOF
)
echo $LIBRARIES
for i in $LIBRARIES
do
  echo $i
  if [ -d /usr/local/swoole-cli/$i ];then
    touch /usr/local/swoole-cli/$i/.completed
  fi
done

exit 0 ;
LIBRARIES=$(echo $LIBRARIES | sed 's/ /,/g')


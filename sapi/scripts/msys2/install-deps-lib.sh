#!/usr/bin/env bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../../
  pwd
)
cd ${__DIR__}
bash install-libzip.sh
bash install-libsodium.sh
bash install-liboniguruma.sh
bash install-libpq.sh
bash install-libgif.sh
bash install-libpng.sh
bash install-freetype.sh
bash install-libjpeg.sh
bash install-libwebp.sh
bash install-libyuv.sh
bash install-libaom.sh
bash install-libgav1.sh
bash install-libavif.sh
bash install-imagemagick.sh


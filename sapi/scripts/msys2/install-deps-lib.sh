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

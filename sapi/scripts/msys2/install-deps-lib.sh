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

# --- enable ccache to accelerate repeated CI builds (no-op when ccache is missing) ---
# 该脚本以子进程调用 install-*.sh，PATH/CCACHE_DIR 会随之继承
if command -v ccache >/dev/null 2>&1; then
    CCACHE_BIN_DIR="${__PROJECT__}/var/ccache-bin"
    CCACHE_DATA_DIR="${__PROJECT__}/var/ccache"
    mkdir -p "${CCACHE_BIN_DIR}" "${CCACHE_DATA_DIR}"
    for CCACHE_TOOL in cc gcc c++ g++; do
        test -e "${CCACHE_BIN_DIR}/${CCACHE_TOOL}" || ln -sf "$(command -v ccache)" "${CCACHE_BIN_DIR}/${CCACHE_TOOL}"
    done
    unset CCACHE_TOOL
    export PATH="${CCACHE_BIN_DIR}:${PATH}"
    export CCACHE_DIR="${CCACHE_DATA_DIR}"
    export CCACHE_MAXSIZE="${CCACHE_MAXSIZE:-2G}"
fi

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


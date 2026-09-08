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
cd ${__PROJECT__}
mkdir -p bin/.libs

# --- enable ccache to accelerate repeated CI builds (no-op when ccache is missing) ---
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

# export LDFLAGS="-all-static"
LOGICAL_PROCESSORS=$(nproc)

: <<'EOF'
set +u
if [ -n "${GITHUB_ACTION}" ]; then
  if test $LOGICAL_PROCESSORS -ge 4; then
    LOGICAL_PROCESSORS=$((LOGICAL_PROCESSORS - 2))
  fi
  make
  # make -j $LOGICAL_PROCESSORS
else
  make -j $LOGICAL_PROCESSORS
fi
set -u
EOF
make -j $LOGICAL_PROCESSORS

./bin/swoole-cli -v

cd ${__PROJECT__}
APP_VERSION=$(./bin/swoole-cli -v | awk 'NR==1{print $2}')
APP_NAME='swoole-cli'
echo "v${APP_VERSION}" >${__PROJECT__}/APP_VERSION
echo ${APP_NAME} >${__PROJECT__}/APP_NAME

#!/usr/bin/env bash
#
# 获取 phpx 源码到 thirdparty/phpx
#
# 版本来源：sapi/PHPX-VERSION.conf
# 两种模式（与 swoole 源码一致）：
# 1. 固定版本（如 v2.6.12）：下载 GitHub tag 归档，解压到 thirdparty/phpx
# 2. master（开发分支）：用 git 管理 —— 不存在则 clone，已存在则 git pull
#
# 网络受限时：curl / git 会读取标准的 https_proxy / http_proxy 环境变量走代理

__DIR__=$(
  cd "$(dirname "$0")" || exit
  pwd
)

WORKDIR=$(
  cd "${__DIR__}"/../../ || exit
  pwd
)

PHPX_VERSION=$(awk 'NR==1{ print $1 }' "sapi/PHPX-VERSION.conf")
PHPX_VERSION_NUM="${PHPX_VERSION#v}"

cd "${WORKDIR}" || exit

PHPX_DIR="phpx"

# ---------------------------------------------------------------------------
# master 开发分支：用 git 管理
# ---------------------------------------------------------------------------
if [ "${PHPX_VERSION_NUM}" = "master" ]; then
    if [ -d "${PHPX_DIR}/.git" ]; then
        echo "updating phpx master (git pull)"
        git -C "${PHPX_DIR}" pull --ff-only || { echo "git pull failed" >&2; exit 1; }
    else
        test -d "${PHPX_DIR}" && rm -rf "${PHPX_DIR}"
        echo "cloning phpx master"
        git clone -b master https://github.com/swoole/phpx.git "${PHPX_DIR}" || { echo "git clone failed" >&2; exit 1; }
    fi
    exit 0
fi

# ---------------------------------------------------------------------------
# 固定版本：下载 GitHub tag 归档
# ---------------------------------------------------------------------------
if [ ! -f "${PHPX_DIR}/CMakeLists.txt" ]; then
    TGZ_FILE="${WORKDIR}/pool/lib/phpx-${PHPX_VERSION}.tar.gz"
    if [ ! -f "$TGZ_FILE" ]; then
        echo "downloading phpx-${PHPX_VERSION}.tar.gz"
        curl -fSL "https://github.com/swoole/phpx/archive/refs/tags/${PHPX_VERSION}.tar.gz" -o "$TGZ_FILE" \
            || { echo "download phpx-src failed" >&2; exit 1; }
        if [ ! -s "$TGZ_FILE" ]; then
            echo "download phpx-src failed: ${TGZ_FILE}" >&2
            rm -f "$TGZ_FILE"
            exit 1
        fi
    fi

    echo "unpacking phpx-${PHPX_VERSION}.tar.gz"
    mkdir -p "${PHPX_DIR}"
    tar --strip-components=1 -C "${PHPX_DIR}" -xf "$TGZ_FILE" || { echo "unpack failed" >&2; exit 1; }
fi

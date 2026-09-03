#!/usr/bin/env bash
#
# 下载 swoole-src 源码归档到 pool/ext/，并解压到 ext/swoole
#
# 版本来源：sapi/SWOOLE-VERSION.conf
# 下载源：优先 GitHub tag 归档，失败回退 PECL（二者解压后顶层目录名不同，
#         但 --strip-components=1 之后得到的是同一份 swoole 源码根）
#
# 网络受限时：curl 会读取标准的 https_proxy / http_proxy 环境变量走代理，
# 例如：https_proxy=http://127.0.0.1:1080 php prepare.php

__DIR__=$(
  cd "$(dirname "$0")" || exit
  pwd
)

WORKDIR=$(
  cd "${__DIR__}"/../../ || exit
  pwd
)

ORIGIN_SWOOLE_VERSION=$(awk 'NR==1{ print $1 }' "sapi/SWOOLE-VERSION.conf")
# 去掉 v 前缀，与 ext/swoole/CMakeLists.txt 里的版本格式（如 6.2.0）保持一致
SWOOLE_VERSION="${ORIGIN_SWOOLE_VERSION#v}"
CURRENT_SWOOLE_VERSION=''

cd "${WORKDIR}" || exit

TGZ_FILE="${WORKDIR}/pool/ext/swoole-${ORIGIN_SWOOLE_VERSION}.tgz"
SWOOLE_DIR="${WORKDIR}/ext/swoole/"

if [ -f "ext/swoole/CMakeLists.txt" ] ;then
    CURRENT_SWOOLE_VERSION=$(grep 'set(SWOOLE_VERSION' ext/swoole/CMakeLists.txt | awk '{ print $2 }' | sed 's/)//')
    if [[ "${CURRENT_SWOOLE_VERSION}" =~ "-dev" ]]; then
        echo 'swoole version master'
        if [ -n "${GITHUB_ACTION}" ]; then
            test -f "$TGZ_FILE" && rm -f "$TGZ_FILE"
            CURRENT_SWOOLE_VERSION=''
        fi
    fi
fi

if [ "${SWOOLE_VERSION}" != "${CURRENT_SWOOLE_VERSION}" ] ;then
    if [ ! -f "$TGZ_FILE" ] ;then
        SWOOLE_VERSION_NUM="${ORIGIN_SWOOLE_VERSION#v}"
        echo "downloading swoole-${ORIGIN_SWOOLE_VERSION}.tgz"

        # 优先 GitHub tag 归档，失败回退 PECL
        curl -fSL "https://github.com/swoole/swoole-src/archive/refs/tags/${ORIGIN_SWOOLE_VERSION}.tar.gz" -o "$TGZ_FILE" \
        || curl -fSL "https://pecl.php.net/get/swoole-${SWOOLE_VERSION_NUM}.tgz" -o "$TGZ_FILE"

        # 校验下载产物（-s 判断文件存在且非空）
        if [ ! -s "$TGZ_FILE" ]; then
            echo "download swoole-src failed: ${TGZ_FILE}" >&2
            rm -f "$TGZ_FILE"
            exit 1
        fi
    fi

    # 版本不一致时删除旧目录，重新解压新版本
    echo "unpacking swoole-${ORIGIN_SWOOLE_VERSION}.tgz"
    test -d "$SWOOLE_DIR" && rm -rf "$SWOOLE_DIR"
    mkdir -p "${SWOOLE_DIR}"
    tar --strip-components=1 -C "${SWOOLE_DIR}" -xf "$TGZ_FILE"
fi

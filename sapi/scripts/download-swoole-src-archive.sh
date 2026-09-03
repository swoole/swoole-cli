#!/usr/bin/env bash
#
# 获取 swoole-src 源码到 ext/swoole
#
# 版本来源：sapi/SWOOLE-VERSION.conf
#
# 两种模式：
# 1. 固定版本（如 v6.2.2）：下载源码归档（优先 GitHub tag 归档，失败回退 PECL），
#    解压到 ext/swoole，归档保存到 pool/ext/
# 2. master（开发分支，滚动更新）：用 git 管理 —— ext/swoole 不存在则 clone，
#    已存在则 git pull 更新到最新
#
# 网络受限时：curl / git 会读取标准的 https_proxy / http_proxy 环境变量走代理，
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

cd "${WORKDIR}" || exit

SWOOLE_DIR="ext/swoole"

# ---------------------------------------------------------------------------
# master 开发分支：用 git 管理，每次更新到最新
# ---------------------------------------------------------------------------
if [ "${SWOOLE_VERSION}" = "master" ]; then
    if [ -d "${SWOOLE_DIR}/.git" ]; then
        echo "updating swoole master (git pull)"
        git -C "${SWOOLE_DIR}" pull --ff-only || { echo "git pull failed" >&2; exit 1; }
    else
        # 不存在，或存在但不是 git 仓库（之前是固定版本解压出来的源码）
        test -d "${SWOOLE_DIR}" && rm -rf "${SWOOLE_DIR}"
        echo "cloning swoole master"
        git clone -b master https://github.com/swoole/swoole-src.git "${SWOOLE_DIR}" || { echo "git clone failed" >&2; exit 1; }
    fi
    exit 0
fi

# ---------------------------------------------------------------------------
# 固定版本：下载源码归档并解压
# ---------------------------------------------------------------------------
TGZ_FILE="${WORKDIR}/pool/ext/swoole-${ORIGIN_SWOOLE_VERSION}.tgz"

CURRENT_SWOOLE_VERSION=''
if [ -f "ext/swoole/CMakeLists.txt" ] ;then
    CURRENT_SWOOLE_VERSION=$(grep 'set(SWOOLE_VERSION' ext/swoole/CMakeLists.txt | awk '{ print $2 }' | sed 's/)//')
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
    test -d "${SWOOLE_DIR}" && rm -rf "${SWOOLE_DIR}"
    mkdir -p "${SWOOLE_DIR}"
    tar --strip-components=1 -C "${SWOOLE_DIR}" -xf "$TGZ_FILE"
fi

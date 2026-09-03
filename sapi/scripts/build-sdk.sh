#!/usr/bin/env bash
# 打包 swoole-cli SDK：静态库（libphp.a / libphpx.a）+ 头文件，用于分发给用户。
#
# 头文件来源：
#   - PHP 内核：/work 下 main/ Zend/ TSRM/ ext/ 的 *.h
#   - phpx：/work/thirdparty/phpx/include/
#   - 第三方库：/usr/local/swoole-cli/<库>/include/
#
# 静态库只打包 libphp.a 与 libphpx.a；第三方库的 .a 不打包，
# 因为它们的符号已经合并进 libphp.a 了。

set -e

WORK_DIR=${WORK_DIR:-/work}
GLOBAL_PREFIX=${GLOBAL_PREFIX:-/usr/local/swoole-cli}

SWOOLE_VERSION=$(awk 'NR==1{ print $1 }' "${WORK_DIR}/sapi/SWOOLE-VERSION.conf")
PHP_VERSION=$(awk 'NR==1{ print $1 }' "${WORK_DIR}/sapi/PHP-VERSION.conf")

OS=linux
ARCH=$(uname -m)
case "${ARCH}" in
    x86_64) ARCH="x64" ;;
    aarch64) ARCH="arm64" ;;
esac

SDK_NAME="swoole-cli-sdk-v${SWOOLE_VERSION#v}-php${PHP_VERSION}-${OS}-${ARCH}"
DIST_DIR="${WORK_DIR}/sdk"
SDK_ROOT="${DIST_DIR}/${SDK_NAME}"

echo "=============================[sdk]==============================="
echo "sdk name      : ${SDK_NAME}"
echo "swoole        : ${SWOOLE_VERSION}"
echo "php           : ${PHP_VERSION}"
echo "arch          : ${ARCH}"
echo "----------------------------------------------------------------------"

rm -rf "${SDK_ROOT}"
mkdir -p "${SDK_ROOT}/lib" "${SDK_ROOT}/include/php" "${SDK_ROOT}/include/phpx"

# 1. 静态库
if [ -f "${WORK_DIR}/libs/libphp.a" ]; then
    cp -f "${WORK_DIR}/libs/libphp.a" "${SDK_ROOT}/lib/"
else
    echo "[sdk] 未找到 libs/libphp.a，请先执行 ./make.sh libphp" >&2
    exit 1
fi
if [ -f "${WORK_DIR}/libs/libphpx.a" ]; then
    cp -f "${WORK_DIR}/libs/libphpx.a" "${SDK_ROOT}/lib/"
else
    echo "[sdk] 未找到 libs/libphpx.a，请先执行 ./make.sh phpx" >&2
    exit 1
fi

# 2. PHP 内核头文件（只收集 *.h，保持目录结构）
(
    cd "${WORK_DIR}"
    find main Zend TSRM ext -name '*.h' -print \
        | tar -cf - -T - \
        | tar -xf - -C "${SDK_ROOT}/include/php/"
)

# 3. phpx 头文件
(
    cd "${WORK_DIR}/thirdparty/phpx/include"
    find . -name '*.h' -print \
        | tar -cf - -T - \
        | tar -xf - -C "${SDK_ROOT}/include/phpx/"
)

# 4. 第三方库头文件（直接平铺到 include/ 根，保持各自的相对子目录，如 openssl/、curl/）
for inc in "${GLOBAL_PREFIX}"/*/include; do
    [ -d "$inc" ] || continue
    (
        cd "$inc"
        find . -name '*.h' -print \
            | tar -cf - -T - \
            | tar -xf - -C "${SDK_ROOT}/include/"
    )
done

# 5. 打包
mkdir -p "${DIST_DIR}"
cd "${DIST_DIR}"
tar -cJf "${SDK_NAME}.tar.xz" "${SDK_NAME}"

echo "----------------------------------------------------------------------"
echo "archive       : ${DIST_DIR}/${SDK_NAME}.tar.xz"
echo "size          : $(du -h "${DIST_DIR}/${SDK_NAME}.tar.xz" | cut -f1)"
echo "libs          :"
ls -la "${SDK_ROOT}/lib/"
echo "======================================================================"

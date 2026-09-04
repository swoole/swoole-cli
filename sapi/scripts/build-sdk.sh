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
#
# 另外打包 musl 的启动文件（crt1.o / crti.o / crtn.o）到 lib/musl/：
# libphp.a 内嵌的是 musl libc，最终可执行文件必须用 musl 的 C 运行时链接。

set -e

WORK_DIR=${WORK_DIR:-/work}
GLOBAL_PREFIX=${GLOBAL_PREFIX:-/usr/local/swoole-cli}
CC=${CC:-clang}

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

# 1.1 musl 启动文件
#     最终链接必须用 musl 的 crt1.o。若混进 glibc 的 crt1.o，它的 _start 会把
#     控制权交给 libphp.a 里 musl 的 __libc_start_main，后者安装的线程指针布局
#     与链接器（按 glibc 目标）算出的 TLS 偏移不一致，PHP 的 __thread 变量
#     （ZTS 模块全局）会读到错误地址，进程在 php_module_startup 阶段崩溃。
CRT_SRC=""
CRT_PATH="$(${CC} -print-file-name=crt1.o 2>/dev/null || true)"
if [ -n "${CRT_PATH}" ] && [ "${CRT_PATH}" != "crt1.o" ] && [ -f "${CRT_PATH}" ]; then
    CRT_SRC="$(dirname "${CRT_PATH}")"
else
    for candidate in /usr/lib /lib; do
        if [ -f "${candidate}/crt1.o" ]; then
            CRT_SRC="${candidate}"
            break
        fi
    done
fi

if [ -z "${CRT_SRC}" ]; then
    echo "[sdk] 未找到 musl crt1.o，无法产出可用于 --full-static 的 SDK" >&2
    exit 1
fi

mkdir -p "${SDK_ROOT}/lib/musl"
for f in crt1.o crti.o crtn.o rcrt1.o Scrt1.o; do
    [ -f "${CRT_SRC}/${f}" ] && cp -f "${CRT_SRC}/${f}" "${SDK_ROOT}/lib/musl/"
done

if [ ! -f "${SDK_ROOT}/lib/musl/crt1.o" ]; then
    echo "[sdk] musl crt1.o 拷贝失败（源目录：${CRT_SRC}）" >&2
    exit 1
fi
echo "musl crt      : ${CRT_SRC} -> lib/musl/"

# 2. PHP 内核头文件（只收集 *.h，保持目录结构；含 sapi/embed/php_embed.h）
(
    cd "${WORK_DIR}"
    find main Zend TSRM ext sapi/embed -name '*.h' -print \
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

# 3.1 mpdecimal 头（phpx 的 decimal 依赖）
#     phpx_decimal.h 用 #include <decimal.hh>，是 phpx 公开 API 的一部分，
#     放在 include/phpx/ 下与 phpx 自身头文件同目录，
#     用户 -I {SDK}/include/phpx 即可一并找到，不必再额外加 include 路径。
#     （wren_gc.h 不导出：只被 phpx 内部 native_gc.cc 引用，公开头完全抽象了 wren_gc）
for h in \
    "${WORK_DIR}/thirdparty/phpx/thirdparty/mpdecimal/libmpdec++/decimal.hh" \
    "${WORK_DIR}/thirdparty/phpx/thirdparty/mpdecimal/libmpdec/mpdecimal.h"; do
    [ -f "$h" ] && cp -f "$h" "${SDK_ROOT}/include/phpx/"
done

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

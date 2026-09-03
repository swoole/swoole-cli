#!/usr/bin/env bash
# 编译 phpx 全静态库，产出 libs/libphpx.a
#
# 使用 phpx 仓库里的 full-static/ 独立构建目录（只编译 src/core + src/std +
# src/typephp，不含 facade，避免与 <sys/socket.h> 的宏冲突），
# gmp/gmpxx/mpfr 作为 PRIVATE 依赖，其符号由最终链接的自包含 libphp.a 提供。

set -e

WORK_DIR=${WORK_DIR:-/work}
GLOBAL_PREFIX=${GLOBAL_PREFIX:-/usr/local/swoole-cli}

PHPX_DIR="${WORK_DIR}/thirdparty/phpx"
OUTPUT="${WORK_DIR}/libs/libphpx.a"

if [ ! -f "${PHPX_DIR}/full-static/CMakeLists.txt" ]; then
    echo "[phpx] full-static 构建目录不存在（${PHPX_DIR}/full-static）" >&2
    echo "[phpx] 请使用包含 full-static 的 phpx 版本（master 或更新版本）" >&2
    exit 1
fi

echo "===============================[phpx]==============================="
echo "phpx dir     : ${PHPX_DIR}"
echo "php include  : ${WORK_DIR}"
echo "gmp          : ${GLOBAL_PREFIX}/gmp"
echo "mpfr         : ${GLOBAL_PREFIX}/mpfr"
echo "----------------------------------------------------------------------"

# 构建目录放在 /tmp，避免容器(root)产物污染 phpx/ 源码目录（宿主机 swoole 用户
# 无法删除 root 文件，会卡住下一次 git clone/pull）
BUILD_DIR="/tmp/phpx-full-static"
rm -rf "${BUILD_DIR}"

cmake \
    -S "${PHPX_DIR}/full-static" \
    -B "${BUILD_DIR}" \
    -DCMAKE_BUILD_TYPE=Release \
    -DPHPX_PHP_INCLUDE_DIR="${WORK_DIR}" \
    -DPHPX_GMP_INCLUDE_DIR="${GLOBAL_PREFIX}/gmp/include" \
    -DPHPX_GMP_LIB_DIR="${GLOBAL_PREFIX}/gmp/lib" \
    -DPHPX_MPFR_INCLUDE_DIR="${GLOBAL_PREFIX}/mpfr/include" \
    -DPHPX_MPFR_LIB_DIR="${GLOBAL_PREFIX}/mpfr/lib"

cmake --build "${BUILD_DIR}" --parallel "$(nproc 2>/dev/null || echo 4)"

mkdir -p "${WORK_DIR}/libs"
cp -f "${BUILD_DIR}/libphpx.a" "${OUTPUT}"

echo "----------------------------------------------------------------------"
echo "output       : ${OUTPUT}"
echo "size         : $(du -h "${OUTPUT}" | cut -f1)"
echo "======================================================================"

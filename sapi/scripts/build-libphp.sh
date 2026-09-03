#!/usr/bin/env bash
# 生成完全自包含的静态归档 libs/libphp.a
#
# 输入：
#   libs/libphp.a        make 产出的纯 PHP 归档（PHP 内核 + Zend + TSRM + 内置扩展 + embed SAPI）
#   libs.log             由 make.sh config 阶段写入，记录构建 swoole-cli 时解析出的第三方库 -l 列表
#   ldflags.log          由 make.sh config 阶段写入，记录第三方库的 -L 搜索路径
#   <sysroot>/libc.a     musl libc（与 bin/swoole-cli 一样静态并入，不依赖目标机器的 libc）
#   <sysroot>/libstdc++.a / libgcc.a / libgcc_eh.a / libgomp.a  C++ 运行时
#                        （PHP 内核含 C++ 对象，需要 operator new/delete 等符号）
#
# 输出：
#   libs/libphp.a        PHP 目标文件 + 全部第三方静态库 + musl libc + C++ 运行时 合并后的单一归档
#
# 说明：
#   合并使用 ar 的 MRI 模式 ADDLIB，逐成员拷贝，不会丢失同名的归档成员。
#   musl libc.a 位于 ${CC} -print-file-name=libc.a（通常 /usr/lib/libc.a）。
#   musl 把 libm / libpthread / libdl / libcrypt / libresolv / librt 全部并入 libc.a，
#   其余 *.a 都是空壳，因此只需合并 libc.a 一个文件即可。
#   C++ 运行时用的是 libstdc++（clang++ 未指定 -stdlib 时默认，而非 libc++），
#   因此 operator new/delete 等符号合并自 libstdc++.a。

set -e

WORK_DIR=${WORK_DIR:-/work}
GLOBAL_PREFIX=${GLOBAL_PREFIX:-/usr/local/swoole-cli}
CC=${CC:-clang}
CXX=${CXX:-clang++}

# make 产出的纯 PHP 归档（输入），最终产物与之同名（输出）
PHP_ARCHIVE="${WORK_DIR}/libs/libphp.a"
OUTPUT_ARCHIVE="${WORK_DIR}/libs/libphp.a"
LIBS_LOG="${WORK_DIR}/libs.log"
LDFLAGS_LOG="${WORK_DIR}/ldflags.log"

if [ ! -f "${PHP_ARCHIVE}" ]; then
    echo "[libphp] 未找到 ${PHP_ARCHIVE}，请先执行 ./make.sh build 或 make libs/libphp.a"
    exit 1
fi

for f in "${LIBS_LOG}" "${LDFLAGS_LOG}"; do
    if [ ! -f "$f" ]; then
        echo "[libphp] 未找到 $f，请先执行 ./make.sh config"
        exit 1
    fi
done

# 定位 musl libc.a
LIBC_ARCHIVE="$(${CC} -print-file-name=libc.a 2>/dev/null || true)"
if [ -z "${LIBC_ARCHIVE}" ] || [ "${LIBC_ARCHIVE}" = "libc.a" ] || [ ! -f "${LIBC_ARCHIVE}" ]; then
    # clang 找不到时回退到常见 musl 路径
    for candidate in /usr/lib/libc.a /lib/libc.a; do
        if [ -f "$candidate" ]; then
            LIBC_ARCHIVE="$candidate"
            break
        fi
    done
fi
if [ -z "${LIBC_ARCHIVE}" ] || [ ! -f "${LIBC_ARCHIVE}" ]; then
    echo "[libphp] 未找到 musl libc.a，无法产出与 bin/swoole-cli 同等自包含的归档"
    exit 1
fi

# 定位 C++ 运行时静态库。
# swoole-cli 用 clang++ 编译且未指定 -stdlib，因此 C++ 运行时是 libstdc++
# （而不是 libc++）；operator new/delete、std::string 等符号都在 libstdc++.a 里。
# libgcc / libgcc_eh 是 clang++ 链接时自动附加的编译器运行时；libgomp 是 OpenMP。
locate_archive() {
    local lib="$1"
    local locator="$2"
    local path
    path="$("${locator}" -print-file-name="${lib}" 2>/dev/null || true)"
    if [ -n "$path" ] && [ "$path" != "$lib" ] && [ -f "$path" ]; then
        echo "$path"
    fi
}

CPP_RUNTIME_ARCHIVES=()
for lib in libstdc++.a libgomp.a; do
    path="$(locate_archive "$lib" "$CXX")"
    [ -n "$path" ] && CPP_RUNTIME_ARCHIVES+=("$path")
done
for lib in libgcc.a libgcc_eh.a; do
    path="$(locate_archive "$lib" "$CC")"
    [ -n "$path" ] && CPP_RUNTIME_ARCHIVES+=("$path")
done

# 收集 -L 搜索路径
LIB_DIRS=()
while read -r flag; do
    case "$flag" in
        -L*) LIB_DIRS+=("${flag#-L}") ;;
    esac
done < <(tr ' ' '\n' < "${LDFLAGS_LOG}")

# 收集 -l 库名
LIB_NAMES=()
while read -r flag; do
    case "$flag" in
        -l*) LIB_NAMES+=("${flag#-l}") ;;
    esac
done < <(tr ' ' '\n' < "${LIBS_LOG}")

# 把 -l 名称解析成归档路径
# 只合并 ${GLOBAL_PREFIX} 下由 swoole-cli 自行编译的静态库；
# 其余（libstdc++ / libgomp / libgcc 等）属于工具链运行时，链接时由编译器提供。
ARCHIVES=()
SYSTEM_LIBS=()
for name in "${LIB_NAMES[@]}"; do
    found=""
    for dir in "${LIB_DIRS[@]}"; do
        if [ -f "${dir}/lib${name}.a" ]; then
            found="${dir}/lib${name}.a"
            break
        fi
    done
    if [ -z "$found" ]; then
        SYSTEM_LIBS+=("$name")
        continue
    fi
    case "$found" in
        "${GLOBAL_PREFIX}"/*)
            dup=0
            for a in ${ARCHIVES[@]+"${ARCHIVES[@]}"}; do
                [ "$a" = "$found" ] && dup=1 && break
            done
            [ "$dup" -eq 0 ] && ARCHIVES+=("$found")
            ;;
        *)
            SYSTEM_LIBS+=("$name")
            ;;
    esac
done

echo "===============================[libphp]==============================="
echo "php archive  : ${PHP_ARCHIVE}"
echo "global prefix: ${GLOBAL_PREFIX}"
echo "musl libc    : ${LIBC_ARCHIVE}"
echo "c++ runtime  : ${CPP_RUNTIME_ARCHIVES[*]:-(无)}"
echo "lib dirs     : ${#LIB_DIRS[@]}"
echo "lib names    : ${#LIB_NAMES[@]}"
echo "merged(.a)   : ${#ARCHIVES[@]}"
echo "system libs  : ${SYSTEM_LIBS[*]}"
echo "----------------------------------------------------------------------"

mkdir -p "${WORK_DIR}/libs"

# 清理旧流程遗留的中间产物（现在只产出单一的 libs/libphp.a）
rm -f "${WORK_DIR}/libs/libphp-core.a"

# 输出与输入同名，先把纯 PHP 归档挪到临时位置，避免 CREATE 提前覆盖掉输入
TMP_PHP_ARCHIVE="$(mktemp "${WORK_DIR}/libs/.libphp-input.XXXXXX")"
mv -f "${PHP_ARCHIVE}" "${TMP_PHP_ARCHIVE}"
rm -f "${OUTPUT_ARCHIVE}"

# 用 ar 的 MRI 模式按顺序合并：
# PHP 目标文件 -> 第三方静态库 -> musl libc -> C++ 运行时（libgcc/libgcc_eh -> libstdc++/libgomp）
{
    echo "CREATE ${OUTPUT_ARCHIVE}"
    echo "ADDLIB ${TMP_PHP_ARCHIVE}"
    for a in "${ARCHIVES[@]}"; do
        echo "ADDLIB $a"
    done
    echo "ADDLIB ${LIBC_ARCHIVE}"
    for a in "${CPP_RUNTIME_ARCHIVES[@]}"; do
        echo "ADDLIB $a"
    done
    echo "SAVE"
    echo "END"
} | ar -M

rm -f "${TMP_PHP_ARCHIVE}"

# 重建符号索引（ADDLIB 合并后索引可能不完整）
ranlib "${OUTPUT_ARCHIVE}"

echo "----------------------------------------------------------------------"
echo "output       : ${OUTPUT_ARCHIVE}"
echo "members      : $(ar t "${OUTPUT_ARCHIVE}" | wc -l)"
echo "size         : $(du -h "${OUTPUT_ARCHIVE}" | cut -f1)"
echo ""
echo "[libphp] 校验 embed SAPI 符号："
if nm -g --defined-only "${OUTPUT_ARCHIVE}" 2>/dev/null | grep -qw 'php_embed_init'; then
    echo "  php_embed_init      OK"
else
    echo "  php_embed_init      MISSING"
    exit 1
fi
if nm -g --defined-only "${OUTPUT_ARCHIVE}" 2>/dev/null | grep -qw 'php_embed_shutdown'; then
    echo "  php_embed_shutdown  OK"
else
    echo "  php_embed_shutdown  MISSING"
    exit 1
fi
echo ""
echo "[libphp] 校验 musl libc 符号："
if nm -g --defined-only "${OUTPUT_ARCHIVE}" 2>/dev/null | grep -qw 'printf'; then
    echo "  printf (musl libc)  OK"
else
    echo "  printf (musl libc)  MISSING"
    exit 1
fi
echo ""
echo "[libphp] 校验 C++ 运行时符号："
if nm -g --defined-only "${OUTPUT_ARCHIVE}" 2>/dev/null | grep -qw '_Znwm'; then
    echo "  operator new(_Znwm)   OK"
else
    echo "  operator new(_Znwm)   MISSING"
    exit 1
fi
if nm -g --defined-only "${OUTPUT_ARCHIVE}" 2>/dev/null | grep -qw '_ZdlPv'; then
    echo "  operator delete(_ZdlPv) OK"
else
    echo "  operator delete(_ZdlPv) MISSING"
    exit 1
fi
echo "======================================================================"

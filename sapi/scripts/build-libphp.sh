#!/usr/bin/env bash
# 生成完全自包含的静态归档 libs/libphp.a
#
# 输入：
#   libs/libphp-core.a   make 产出的纯 PHP 归档（PHP 内核 + Zend + TSRM + 内置扩展 + embed SAPI）
#   libs.log             由 make.sh config 阶段写入，记录构建 swoole-cli 时解析出的第三方库 -l 列表
#   ldflags.log          由 make.sh config 阶段写入，记录第三方库的 -L 搜索路径
#
# 输出：
#   libs/libphp.a        PHP 目标文件 + 全部 swoole-cli 自编译第三方静态库合并后的单一归档
#
# 说明：
#   合并使用 ar 的 MRI 模式 ADDLIB，逐成员拷贝，不会丢失同名的归档成员。
#   系统库（libc / libm / libpthread / libdl / libstdc++ / libgomp / libgcc）不并入归档，
#   仍由链接器在 -static 时自动提供，参见 docs/libphp.md。

set -e

WORK_DIR=${WORK_DIR:-/work}
GLOBAL_PREFIX=${GLOBAL_PREFIX:-/usr/local/swoole-cli}

CORE_ARCHIVE="${WORK_DIR}/libs/libphp-core.a"
OUTPUT_ARCHIVE="${WORK_DIR}/libs/libphp.a"
LIBS_LOG="${WORK_DIR}/libs.log"
LDFLAGS_LOG="${WORK_DIR}/ldflags.log"

if [ ! -f "${CORE_ARCHIVE}" ]; then
    echo "[libphp] 未找到 ${CORE_ARCHIVE}，请先执行 ./make.sh build 或 make libs/libphp.a"
    exit 1
fi

for f in "${LIBS_LOG}" "${LDFLAGS_LOG}"; do
    if [ ! -f "$f" ]; then
        echo "[libphp] 未找到 $f，请先执行 ./make.sh config"
        exit 1
    fi
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
# 其余（libc / libm / libpthread / libstdc++ / libgomp 等）属于工具链提供的系统库，交给链接器。
ARCHIVES=()
SYSTEM_LIBS=()
MISSING=()
for name in "${LIB_NAMES[@]}"; do
    found=""
    for dir in "${LIB_DIRS[@]}"; do
        if [ -f "${dir}/lib${name}.a" ]; then
            found="${dir}/lib${name}.a"
            break
        fi
    done
    if [ -z "$found" ]; then
        # 在搜索路径里找到的是动态库，或根本找不到，都按系统库处理
        SYSTEM_LIBS+=("$name")
        continue
    fi
    case "$found" in
        "${GLOBAL_PREFIX}"/*)
            # 去重：同一个归档只合并一次
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
echo "core archive : ${CORE_ARCHIVE}"
echo "global prefix: ${GLOBAL_PREFIX}"
echo "lib dirs     : ${#LIB_DIRS[@]}"
echo "lib names    : ${#LIB_NAMES[@]}"
echo "merged(.a)   : ${#ARCHIVES[@]}"
echo "system libs  : ${SYSTEM_LIBS[*]}"
if [ ${#MISSING[@]} -gt 0 ]; then
    echo "missing      : ${MISSING[*]}"
fi
echo "----------------------------------------------------------------------"

mkdir -p "${WORK_DIR}/libs"
rm -f "${OUTPUT_ARCHIVE}"

# 用 ar 的 MRI 模式把 core 与全部第三方归档逐个成员合并
{
    echo "CREATE ${OUTPUT_ARCHIVE}"
    echo "ADDLIB ${CORE_ARCHIVE}"
    for a in "${ARCHIVES[@]}"; do
        echo "ADDLIB $a"
    done
    echo "SAVE"
    echo "END"
} | ar -M

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
echo "======================================================================"

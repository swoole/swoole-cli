#!/usr/bin/env bash

set -euo pipefail

WORK_DIR=${WORK_DIR:-$(pwd)}
TARGET=${1:-}
DIST_DIR=${RUNTIME_LAYER_DIST_DIR:-${WORK_DIR}/runtime-layer}

usage()
{
    echo "Usage: $0 <linux-x64|linux-arm64|iphoneos-arm64>" >&2
}

case "${TARGET}" in
    linux-x64|linux-arm64|iphoneos-arm64) ;;
    *) usage; exit 2 ;;
esac

if [[ -z "${GLOBAL_PREFIX:-}" ]]; then
    if [[ "${TARGET}" == iphoneos-arm64 ]]; then
        GLOBAL_PREFIX=${WORK_DIR}/var/iphoneos-arm64/deps
    else
        GLOBAL_PREFIX=/usr/local/swoole-cli
    fi
fi

PHP_VERSION=$(awk 'NR == 1 { print $1 }' "${WORK_DIR}/sapi/PHP-VERSION.conf")
SWOOLE_VERSION=$(awk 'NR == 1 { print $1 }' "${WORK_DIR}/sapi/SWOOLE-VERSION.conf")
PRODUCER_REF=${RUNTIME_LAYER_VERSION:-${GITHUB_REF_NAME:-${GITHUB_SHA:-development}}}
PRODUCER_REF=${PRODUCER_REF//\//-}
PACKAGE_NAME="php-runtime-layer_swoole-cli-${PRODUCER_REF}_php${PHP_VERSION}_${TARGET}"
PACKAGE_ROOT="${DIST_DIR}/${PACKAGE_NAME}"

rm -rf "${PACKAGE_ROOT}"
mkdir -p \
    "${PACKAGE_ROOT}/include/php" \
    "${PACKAGE_ROOT}/include" \
    "${PACKAGE_ROOT}/lib" \
    "${PACKAGE_ROOT}/LICENSES"

required_files=(
    "${WORK_DIR}/libs/libphp.a"
    "${WORK_DIR}/main/php.h"
    "${WORK_DIR}/main/php_config.h"
    "${WORK_DIR}/var/php-${PHP_VERSION}/LICENSE"
)

if [[ "${TARGET}" == iphoneos-arm64 ]]; then
    required_files+=(
        "${GLOBAL_PREFIX}/gmp/include/gmp.h"
        "${GLOBAL_PREFIX}/gmp/include/gmpxx.h"
        "${GLOBAL_PREFIX}/mpfr/include/mpfr.h"
    )
fi

for file in "${required_files[@]}"; do
    if [[ ! -f "${file}" ]]; then
        echo "PHP runtime layer input is missing: ${file}" >&2
        exit 1
    fi
done

(
    cd "${WORK_DIR}"
    find main Zend TSRM ext sapi/embed -name '*.h' -print \
        | tar -cf - -T - \
        | tar -xf - -C "${PACKAGE_ROOT}/include/php"
)

cp -p "${WORK_DIR}/libs/libphp.a" "${PACKAGE_ROOT}/lib/"

if [[ "${TARGET}" == iphoneos-arm64 ]]; then
    if [[ $(uname -s) != Darwin ]]; then
        echo "iphoneos-arm64 must be packaged on macOS." >&2
        exit 1
    fi
    xcrun --sdk iphoneos --show-sdk-path >/dev/null

    if ! grep -Eq '^#define[[:space:]]+ZTS([[:space:]]+1)?([[:space:]]|$)' \
        "${WORK_DIR}/main/php_config.h"; then
        echo "The iPhoneOS PHP runtime layer must use ZTS." >&2
        exit 1
    fi

    cp -p "${GLOBAL_PREFIX}/gmp/include/gmp.h" "${PACKAGE_ROOT}/include/"
    cp -p "${GLOBAL_PREFIX}/gmp/include/gmpxx.h" "${PACKAGE_ROOT}/include/"
    cp -p "${GLOBAL_PREFIX}/mpfr/include/mpfr.h" "${PACKAGE_ROOT}/include/"
    if [[ -f "${GLOBAL_PREFIX}/mpfr/include/mpf2mpfr.h" ]]; then
        cp -p "${GLOBAL_PREFIX}/mpfr/include/mpf2mpfr.h" "${PACKAGE_ROOT}/include/"
    fi

    archs=$(xcrun --sdk iphoneos lipo -archs "${PACKAGE_ROOT}/lib/libphp.a")
    if [[ " ${archs} " != *" arm64 "* ]]; then
        echo "libphp.a does not contain arm64: ${archs}" >&2
        exit 1
    fi
    printf '%s\n' 'typephp-iphoneos-arm64-php-zts-abi-v1' \
        > "${PACKAGE_ROOT}/.typephp-php-runtime-abi"
else
    machine=$(uname -m)
    case "${TARGET}:${machine}" in
        linux-x64:x86_64|linux-arm64:aarch64|linux-arm64:arm64) ;;
        *)
            echo "Build host architecture ${machine} does not match ${TARGET}." >&2
            exit 1
            ;;
    esac
    if ! compgen -G '/lib/ld-musl-*.so.1' >/dev/null; then
        echo "Linux Runtime Layers must be packaged in the musl build environment." >&2
        exit 1
    fi

    for inc in "${GLOBAL_PREFIX}"/*/include; do
        [[ -d "${inc}" ]] || continue
        (
            cd "${inc}"
            find . -name '*.h' -print \
                | tar -cf - -T - \
                | tar -xf - -C "${PACKAGE_ROOT}/include"
        )
    done

    CC=${CC:-clang}
    crt_path=$(${CC} -print-file-name=crt1.o 2>/dev/null || true)
    crt_dir=
    if [[ -n "${crt_path}" && "${crt_path}" != crt1.o && -f "${crt_path}" ]]; then
        crt_dir=$(dirname "${crt_path}")
    else
        for candidate in /usr/lib /lib; do
            if [[ -f "${candidate}/crt1.o" ]]; then
                crt_dir=${candidate}
                break
            fi
        done
    fi
    if [[ -z "${crt_dir}" ]]; then
        echo "Unable to locate the musl crt1.o required by the full-static SDK." >&2
        exit 1
    fi

    mkdir -p "${PACKAGE_ROOT}/lib/musl"
    for file in crt1.o crti.o crtn.o rcrt1.o Scrt1.o; do
        [[ -f "${crt_dir}/${file}" ]] \
            && cp -p "${crt_dir}/${file}" "${PACKAGE_ROOT}/lib/musl/"
    done
    [[ -f "${PACKAGE_ROOT}/lib/musl/crt1.o" ]]
    printf '%s\n' "typephp-${TARGET}-php-zts-full-static-abi-v1" \
        > "${PACKAGE_ROOT}/.typephp-php-runtime-abi"
fi

cp -p "${WORK_DIR}/var/php-${PHP_VERSION}/LICENSE" \
    "${PACKAGE_ROOT}/LICENSES/PHP-LICENSE"
if [[ -f "${WORK_DIR}/ext/swoole/LICENSE" ]]; then
    cp -p "${WORK_DIR}/ext/swoole/LICENSE" \
        "${PACKAGE_ROOT}/LICENSES/SWOOLE-LICENSE"
fi
if [[ -f "${WORK_DIR}/bin/LICENSE" ]]; then
    cp -p "${WORK_DIR}/bin/LICENSE" \
        "${PACKAGE_ROOT}/LICENSES/RUNTIME-DEPENDENCIES"
fi

PRODUCER_REVISION=${GITHUB_SHA:-$(git -C "${WORK_DIR}" rev-parse HEAD)}
ZTS=false
if grep -Eq '^#define[[:space:]]+ZTS([[:space:]]+1)?([[:space:]]|$)' \
    "${WORK_DIR}/main/php_config.h"; then
    ZTS=true
fi

cat > "${PACKAGE_ROOT}/manifest.json" <<EOF
{
  "schema": "typephp-php-runtime-layer-v1",
  "producer": "swoole/swoole-cli",
  "producer_ref": "${PRODUCER_REF}",
  "producer_revision": "${PRODUCER_REVISION}",
  "php_version": "${PHP_VERSION}",
  "swoole_version": "${SWOOLE_VERSION}",
  "target": "${TARGET}",
  "zts": ${ZTS}
}
EOF

mkdir -p "${DIST_DIR}"
ARCHIVE="${DIST_DIR}/${PACKAGE_NAME}.tar.xz"
tar -C "${DIST_DIR}" -cJf "${ARCHIVE}" "${PACKAGE_NAME}"

if command -v sha256sum >/dev/null 2>&1; then
    (cd "${DIST_DIR}" && sha256sum "${PACKAGE_NAME}.tar.xz" \
        > "${PACKAGE_NAME}.tar.xz.sha256")
else
    digest=$(shasum -a 256 "${ARCHIVE}" | awk '{ print $1 }')
    printf '%s  %s\n' "${digest}" "${PACKAGE_NAME}.tar.xz" \
        > "${ARCHIVE}.sha256"
fi

if [[ -n "${GITHUB_OUTPUT:-}" ]]; then
    echo "name=${PACKAGE_NAME}" >> "${GITHUB_OUTPUT}"
    echo "archive=${ARCHIVE}" >> "${GITHUB_OUTPUT}"
fi

echo "Packaged PHP runtime layer: ${ARCHIVE}"

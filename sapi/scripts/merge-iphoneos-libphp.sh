#!/usr/bin/env bash

set -euo pipefail

WORK_DIR=${WORK_DIR:?WORK_DIR is required}
GLOBAL_PREFIX=${GLOBAL_PREFIX:?GLOBAL_PREFIX is required}
PHP_ARCHIVE=${WORK_DIR}/libs/libphp.a

if [[ $(uname -s) != Darwin ]]; then
    echo "The iPhoneOS libphp archive must be merged on macOS." >&2
    exit 1
fi
xcrun --sdk iphoneos --show-sdk-path >/dev/null

archives=(
    "${PHP_ARCHIVE}"
    "${GLOBAL_PREFIX}/gmp/lib/libgmp.a"
    "${GLOBAL_PREFIX}/gmp/lib/libgmpxx.a"
    "${GLOBAL_PREFIX}/mpfr/lib/libmpfr.a"
)
for archive in "${archives[@]}"; do
    if [[ ! -s "${archive}" ]]; then
        echo "iPhoneOS libphp input is missing: ${archive}" >&2
        exit 1
    fi
    archs=$(xcrun --sdk iphoneos lipo -archs "${archive}")
    if [[ " ${archs} " != *" arm64 "* ]]; then
        echo "iPhoneOS archive does not contain arm64: ${archive} (${archs})" >&2
        exit 1
    fi
done

temporary=$(mktemp "${WORK_DIR}/libs/libphp.merged.XXXXXX")
trap 'rm -f "${temporary}"' EXIT
xcrun --sdk iphoneos libtool -static -o "${temporary}" "${archives[@]}"
xcrun --sdk iphoneos ranlib "${temporary}"

members=$(xcrun --sdk iphoneos ar -t "${temporary}" | wc -l | tr -d ' ')
if [[ "${members}" -eq 0 ]]; then
    echo "Merged iPhoneOS libphp.a is empty." >&2
    exit 1
fi
mv -f "${temporary}" "${PHP_ARCHIVE}"
trap - EXIT

echo "Merged iPhoneOS PHP and third-party objects into ${PHP_ARCHIVE} (${members} members)"

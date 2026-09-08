#!/usr/bin/env bash

set -euo pipefail

WORK_DIR=${WORK_DIR:?WORK_DIR is required}
GLOBAL_PREFIX=${GLOBAL_PREFIX:?GLOBAL_PREFIX is required}
AR=${AR:?AR is required}
RANLIB=${RANLIB:?RANLIB is required}
READELF=${READELF:?READELF is required}
PHP_ARCHIVE=${WORK_DIR}/libs/libphp.a

archives=(
    "${PHP_ARCHIVE}"
    "${GLOBAL_PREFIX}/gmp/lib/libgmp.a"
    "${GLOBAL_PREFIX}/gmp/lib/libgmpxx.a"
    "${GLOBAL_PREFIX}/mpfr/lib/libmpfr.a"
)
for archive in "${archives[@]}"; do
    if [[ ! -s "${archive}" ]]; then
        echo "Android libphp input is missing: ${archive}" >&2
        exit 1
    fi
done

temporary=$(mktemp "${WORK_DIR}/libs/libphp.merged.XXXXXX")
mri=$(mktemp "${WORK_DIR}/libs/libphp.mri.XXXXXX")
probe_dir=$(mktemp -d "${WORK_DIR}/libs/libphp.probe.XXXXXX")
trap 'rm -f "${temporary}" "${mri}"; rm -rf "${probe_dir}"' EXIT

{
    echo "CREATE ${temporary}"
    for archive in "${archives[@]}"; do
        echo "ADDLIB ${archive}"
    done
    echo SAVE
    echo END
} > "${mri}"
"${AR}" -M < "${mri}"
"${RANLIB}" "${temporary}"

first_member=$("${AR}" -t "${temporary}" | sed -n '1p')
if [[ -z "${first_member}" ]]; then
    echo "Merged Android libphp.a is empty." >&2
    exit 1
fi
(
    cd "${probe_dir}"
    "${AR}" -x "${temporary}" "${first_member}"
)
if ! "${READELF}" -h "${probe_dir}/${first_member}" | grep -q 'Machine:.*AArch64'; then
    echo "Merged Android libphp.a does not contain AArch64 objects." >&2
    exit 1
fi

members=$("${AR}" -t "${temporary}" | wc -l | tr -d ' ')
mv -f "${temporary}" "${PHP_ARCHIVE}"
trap - EXIT
rm -f "${mri}"
rm -rf "${probe_dir}"

echo "Merged Android PHP and third-party objects into ${PHP_ARCHIVE} (${members} members)"

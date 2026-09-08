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
elf_headers=$(mktemp "${WORK_DIR}/libs/libphp.elf-headers.XXXXXX")
trap 'rm -f "${temporary}" "${mri}" "${elf_headers}"' EXIT

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

if [[ -z "$("${AR}" -t "${temporary}" | sed -n '1p')" ]]; then
    echo "Merged Android libphp.a is empty." >&2
    exit 1
fi

# Validate the complete archive instead of relying on its first member. MRI
# archive member order is not an ABI guarantee and differs between ar versions.
if ! "${READELF}" -h "${temporary}" > "${elf_headers}"; then
    echo "Failed to inspect merged Android libphp.a." >&2
    exit 1
fi
if ! awk '
    /Machine:/ {
        found = 1
        if ($0 !~ /AArch64/) {
            invalid = 1
        }
    }
    END { exit !(found && !invalid) }
' "${elf_headers}"; then
    echo "Merged Android libphp.a contains missing or non-AArch64 ELF objects." >&2
    exit 1
fi

members=$("${AR}" -t "${temporary}" | wc -l | tr -d ' ')
mv -f "${temporary}" "${PHP_ARCHIVE}"
trap - EXIT
rm -f "${mri}" "${elf_headers}"

echo "Merged Android PHP and third-party objects into ${PHP_ARCHIVE} (${members} members)"

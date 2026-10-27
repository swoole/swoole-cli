#!/usr/bin/env bash
set -euo pipefail

case "$(uname -s)" in
  Linux) build_jobs=$(nproc) ;;
  Darwin) build_jobs=$(sysctl -n hw.logicalcpu) ;;
  *) echo 'Unsupported CI runner OS' >&2; exit 1 ;;
esac

[[ "${build_jobs}" =~ ^[1-9][0-9]*$ ]]

{
  echo "BUILD_JOBS=${build_jobs}"
  echo "CMAKE_BUILD_PARALLEL_LEVEL=${build_jobs}"
  echo "PHP_COMPILER_JOBS=${build_jobs}"
  echo "CARGO_BUILD_JOBS=${build_jobs}"
  echo "MAKEFLAGS=-j${build_jobs}"
  echo "MFLAGS=-j${build_jobs}"
  echo "TEST_PHP_JOBS=${build_jobs}"
} >> "${GITHUB_ENV}"

echo "CI build jobs: ${build_jobs}"

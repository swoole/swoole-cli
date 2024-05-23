#!/usr/bin/env bash
set -x

__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../../../../
  pwd
)
cd ${__PROJECT__}



# https://www.nasm.us/pub/nasm/releasebuilds/2.16.03/
test -f  nasm-2.16.03-win64.zip || curl -Lo nasm-2.16.03-win64.zip https://www.nasm.us/pub/nasm/releasebuilds/2.16.03/win64/nasm-2.16.03-win64.zip
test -d  nasm && rm -rf  nasm
unzip nasm-2.16.03-win64.zip
mv  nasm-2.16.03 nasm
ls -lh nasm

test -f strawberry-perl-5.38.2.2-64bit.msi ||  curl -Lo strawberry-perl-5.38.2.2-64bit.msi https://github.com/StrawberryPerl/Perl-Dist-Strawberry/releases/download/SP_53822_64bit/strawberry-perl-5.38.2.2-64bit.msi




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

test -d ext && rm -rf ext

php prepare.php --without-docker=1 --skip-download=1 --with-libavif=1 +uuid +apcu +ds +xlswriter +ssh2

ls -lh var/download-box/

bash var/download-box/download_library_use_script_for_windows.sh
bash var/download-box/download_library_use_git.sh
bash sapi/download-box/download-box-dependencies-sync.sh
bash var/download-box/extract-files.sh

git clone -b master --depth=1 https://github.com/php/php-sdk-binary-tools.git
git clone -b php-8.3.7 --depth=1 https://github.com/php/php-src.git

cp -rf ext/* php-src/ext/
ls -lh php-src/ext/


# https://www.nasm.us/pub/nasm/releasebuilds/2.16.03/
test -f  nasm-2.16.03-win64.zip || curl -Lo nasm-2.16.03-win64.zip https://www.nasm.us/pub/nasm/releasebuilds/2.16.03/win64/nasm-2.16.03-win64.zip
test -d nasm && rm -rf nasm
unzip -f  nasm-2.16.03-win64.zip
mv nasm-2.16.03-win64 nasm
ls -lh nasm

curl -Lo strawberry-perl-5.38.2.2-64bit.msi https://github.com/StrawberryPerl/Perl-Dist-Strawberry/releases/download/SP_53822_64bit/strawberry-perl-5.38.2.2-64bit.msi


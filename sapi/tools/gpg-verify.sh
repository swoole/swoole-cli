#!/usr/bin/env bash
set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../
  pwd
)
cd ${__PROJECT__}

mkdir -p ${__PROJECT__}/var/gnupg-verify

cd ${__PROJECT__}/var/gnupg-verify

# curl https://www.gnu.org/usenet/usenet-gpg-key.txt
# https://ftp.gnu.org/gnu/gnu-keyring.gpg

# test -f gnu-keyring.gpg || curl -Lo gnu-keyring.gpg https://ftp.gnu.org/gnu/gnu-keyring.gpg
test -f gnu-keyring.gpg || curl -Lo gnu-keyring.gpg https://ftpmirror.gnu.org/gnu/gnu-keyring.gpg
# test -f libiconv-1.17.tar.gz.sig || curl -Lo libiconv-1.17.tar.gz.sig https://ftp.gnu.org/gnu/libiconv/libiconv-1.17.tar.gz.sig
test -f libiconv-1.17.tar.gz.sig || curl -Lo libiconv-1.17.tar.gz.sig https://ftpmirror.gnu.org/gnu/libiconv/libiconv-1.17.tar.gz.sig

{
  gpg --import gnu-keyring.gpg
} || {
  echo $?
}

# gpg --verify file.sig file
gpg --verify libiconv-1.17.tar.gz.sig ${__PROJECT__}/pool/lib/libiconv-1.17.tar.gz

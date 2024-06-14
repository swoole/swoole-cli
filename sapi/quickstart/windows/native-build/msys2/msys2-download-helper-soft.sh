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


# https://github.com/notepad-plus-plus/notepad-plus-plus/
test -f npp.8.6.7.Installer.x64.exe || curl -Lo npp.8.6.7.Installer.x64.exe https://github.com/notepad-plus-plus/notepad-plus-plus/releases/download/v8.6.7/npp.8.6.7.Installer.x64.exe

# https://7-zip.org/
test -f 7z2405-x64.exe || curl -Lo 7z2405-x64.exe https://7-zip.org/a/7z2405-x64.exe


test -f Git-2.45.1-64-bit.exe ||  curl -Lo Git-2.45.1-64-bit.exe https://github.com/git-for-windows/git/releases/download/v2.45.1.windows.1/Git-2.45.1-64-bit.exe

# https://curl.se/windows/

test -f curl-8.8.0_1-win64-mingw.zip ||  curl -Lo curl-8.8.0_1-win64-mingw.zip https://curl.se/windows/dl-8.8.0_1/curl-8.8.0_1-win64-mingw.zip
test -d curl-8.8.0_1-win64-mingw && rm -rf curl-8.8.0_1-win64-mingw
unzip curl-8.8.0_1-win64-mingw.zip

# https://libarchive.org/
test -f libarchive-v3.7.4-amd64.zip ||  curl -Lo libarchive-v3.7.4-amd64.zip https://libarchive.org/downloads/libarchive-v3.7.4-amd64.zip
unzip libarchive-v3.7.4-amd64.zip



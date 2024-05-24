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



test -f npp.8.6.7.Installer.x64.exe || curl -Lo vc_redist.x64.exe https://github.com/notepad-plus-plus/notepad-plus-plus/releases/download/v8.6.7/npp.8.6.7.Installer.x64.exe
test -f 7z2405-x64.exe || curl -Lo 7z2405-x64.exe https://7-zip.org/a/7z2405-x64.exe



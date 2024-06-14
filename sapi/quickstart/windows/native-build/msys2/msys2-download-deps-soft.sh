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

# https://learn.microsoft.com/en-us/vcpkg/examples/installing-and-using-packages
# test -d vcpkg || git clone -b master --depth=1 https://github.com/microsoft/vcpkg

# test -f Microsoft.DesktopAppInstaller_8wekyb3d8bbwe.msixbundle ||  curl -Lo Microsoft.DesktopAppInstaller_8wekyb3d8bbwe.msixbundle https://github.com/microsoft/winget-cli/releases/download/v1.7.11261/Microsoft.DesktopAppInstaller_8wekyb3d8bbwe.msixbundle
# rem powershell "add-appxpackage .\Microsoft.DesktopAppInstaller_8wekyb3d8bbwe.msixbundle"
# rem winget install nasm -i


# winget install nasm -i
# https://repo.or.cz/w/nasm.git
# https://www.nasm.us/pub/nasm/releasebuilds/2.16.03/
# https://github.com/netwide-assembler/nasm/blob/master/INSTALL
# https://github.com/netwide-assembler/nasm.git
# test -d nasm || git clone --depth=1 https://github.com/netwide-assembler/nasm.git
# test -f  nasm-2.16.03-win64.zip || curl -Lo nasm-2.16.03-win64.zip https://www.nasm.us/pub/nasm/releasebuilds/2.16.03/win64/nasm-2.16.03-win64.zip
# https://github.com/jingjingxyk/swoole-cli/releases/tag/t-v0.0.3
test -f  nasm-2.16.03-win64.zip || curl -Lo nasm-2.16.03-win64.zip https://github.com/jingjingxyk/swoole-cli/releases/download/t-v0.0.3/nasm-2.16.03-win64.zip
test -d  nasm && rm -rf  nasm
unzip nasm-2.16.03-win64.zip
mv  nasm-2.16.03 nasm
ls -lh nasm



# https://github.com/StrawberryPerl/Perl-Dist-Strawberry/releases/
test -f strawberry-perl-5.38.2.2-64bit.msi ||  curl -Lo strawberry-perl-5.38.2.2-64bit.msi https://github.com/StrawberryPerl/Perl-Dist-Strawberry/releases/download/SP_53822_64bit/strawberry-perl-5.38.2.2-64bit.msi

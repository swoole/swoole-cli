#!/usr/bin/env bash

set -x
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../../
  pwd
)
cd ${__PROJECT__}

# Show System Environment
uname -s
uname -m
uname -r
echo $HOME
sw_vers
xcodebuild -version
brew config
xcrun --show-sdk-path

MIRROR=''
WITH_UPDATE=0

while [ $# -gt 0 ]; do
  case "$1" in
  --mirror)
    MIRROR="$2"
    ;;
  --update)
    WITH_UPDATE=1
    ;;
  --proxy)
    export HTTP_PROXY="$2"
    export HTTPS_PROXY="$2"
    NO_PROXY="127.0.0.0/8,10.0.0.0/8,100.64.0.0/10,172.16.0.0/12,192.168.0.0/16"
    NO_PROXY="${NO_PROXY},::1/128,fe80::/10,fd00::/8,ff00::/8"
    NO_PROXY="${NO_PROXY},localhost"
    NO_PROXY="${NO_PROXY},.tsinghua.edu.cn,.ustc.edu.cn"
    NO_PROXY="${NO_PROXY},.tencent.com"
    NO_PROXY="${NO_PROXY},ftpmirror.gnu.org"
    NO_PROXY="${NO_PROXY},gitee.com,gitcode.com"
    NO_PROXY="${NO_PROXY},.myqcloud.com,.swoole.com"
    export NO_PROXY="${NO_PROXY},.npmmirror.com"
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

case "$MIRROR" in
china | ustc)
  export HOMEBREW_API_DOMAIN="https://mirrors.ustc.edu.cn/homebrew-bottles/api"

  export HOMEBREW_BREW_GIT_REMOTE="https://mirrors.ustc.edu.cn/brew.git"
  export HOMEBREW_CORE_GIT_REMOTE="https://mirrors.ustc.edu.cn/homebrew-core.git"
  export HOMEBREW_BOTTLE_DOMAIN="https://mirrors.ustc.edu.cn/homebrew-bottles"

  export HOMEBREW_PIP_INDEX_URL="https://pypi.tuna.tsinghua.edu.cn/simple"
  export PIPENV_PYPI_MIRROR="https://pypi.tuna.tsinghua.edu.cn/simple"

  # 参考文档： https://help.mirrors.cernet.edu.cn/homebrew/
  # 参考文档： https://help.mirrors.cernet.edu.cn/homebrew/
  ;;
esac

HOMEBREW_PREFIX=$(brew --prefix)

export HOMEBREW_NO_ANALYTICS=1
export HOMEBREW_INSTALL_FROM_API=1
export HOMEBREW_NO_AUTO_UPDATE=1

if [ ${WITH_UPDATE} -eq 1 ]; then
  unset HOMEBREW_NO_AUTO_UPDATE

  case "$MIRROR" in
  china | ustc)
    brew tap --custom-remote --force-auto-update homebrew/cask https://mirrors.ustc.edu.cn/homebrew-cask.git
    brew tap --custom-remote --force-auto-update homebrew/cask-versions https://mirrors.ustc.edu.cn/homebrew-cask-versions.git
    brew tap --custom-remote --force-auto-update homebrew/services https://mirrors.ustc.edu.cn/homebrew-services.git

    # 参考文档： https://help.mirrors.cernet.edu.cn/homebrew/
    # reset
    # brew tap --custom-remote --force-auto-update homebrew/cask https://github.com/Homebrew/homebrew-cask
    # brew tap --custom-remote --force-auto-update homebrew/cask-versions https://github.com/Homebrew/homebrew-cask-versions
    # brew tap --custom-remote --force-auto-update homebrew/services https://mirrors.ustc.edu.cn/homebrew-services.git
    ;;
  esac
  brew doctor
  brew update
fi

brew install wget curl libtool automake re2c llvm flex bison m4 autoconf
brew install libtool gettext coreutils libunistring pkg-config cmake
# macos 环境下 Homebrew packages :   coreutils binutils 不兼容
# 详见： https://github.com/pyenv/pyenv/wiki/Common-build-problems#keg-only-homebrew-packages-are-forcibly-linked--added-to-path

# 已安装的包 跳过安装
# PACKAGES_1=(wget curl libtool automake re2c llvm flex bison m4 autoconf)
# PACKAGES_2=(libtool gettext coreutils libunistring pkg-config cmake)

# PACKAGES=("${PACKAGES_1[@]}" "${PACKAGES_2[@]}")
# for PACKAGE in "${PACKAGES[@]}"; do
#  brew list "$PACKAGE" &>/dev/null || brew install "$PACKAGE"
# done

which glibtool

ln -sf ${HOMEBREW_PREFIX}/opt/libtool/bin/glibtool ${HOMEBREW_PREFIX}/opt/libtool/bin/libtool
ln -sf ${HOMEBREW_PREFIX}/opt/libtool/bin/glibtoolize ${HOMEBREW_PREFIX}/opt/libtool/bin/libtoolize
export PATH=${HOMEBREW_PREFIX}/opt/libtool/bin/:$PATH

libtoolize --version
libtool --help-all

which glibtool
which libtool

brew install xz zip unzip gzip bzip2 7zip p7zip
brew install git ca-certificates

brew install diffutils
brew install socat
brew install mercurial
brew install meson ninja yasm nasm

brew uninstall --ignore-dependencies --force snappy
brew uninstall --ignore-dependencies --force capstone
brew uninstall --ignore-dependencies --force php

which libtool

brew install meson ninja yasm nasm

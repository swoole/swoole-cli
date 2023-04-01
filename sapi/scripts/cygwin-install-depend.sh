#!/usr/biin/env bash
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

# package search  包搜索
# https://cygwin.com/cgi-bin2/package-grep.cgi?grep=openssl

# 命令行参数
# https://www.cygwin.com/faq/faq.html#faq.setup.cli




# wget http://cygwin.com/setup-x86_64.exe


## 同时安装多个包 参考 https://ardupilot.org/dev/docs/building-setup-windows-cygwin.html

## 命令行参数 https://www.cygwin.com/faq/faq.html#faq.setup.cli

# re2c no found
setup-x86_64.exe  --no-desktop --no-shortcuts --no-startmenu --quiet-mode    --site  http://mirrors.ustc.edu.cn/cygwin/ --packages  git,curl,wget,tar,libtool,bison,gcc-g++,autoconf,automake,openssl,libpcre2-devel,libssl-devel,libcurl-devel,libxml2-devel,libxslt-devel,libgmp-devel,ImageMagick,libpng-devel,libjpeg-devel,libfreetype-devel,libwebp-devel,libsqlite3-devel,zlib-devel,libbz2-devel,liblz4-devel,liblzma-devel,libzip-devel,libicu-devel,libonig-devel,libcares-devel,libsodium-devel,libyaml-devel,libMagick-devel,libzstd-devel,libbrotli-devel,libreadline-devel,libintl-devel,libpq-devel,libssh2-devel,libidn2-devel,gettext-devel,coreutils


exit 0


exit 0


C:\cygwin\bin\bash.exe --norc --noprofile

mv  c:/cygdrive/c/Users/biubiu/Downloads/setup-x86_64.exe C:/cygwin64/bin/


move  C:\Users\biubiu\Downloads\setup-x86_64.exe C:\cygwin64\bin
dir C:\cygwin64\bin\setup-x86_64.exe
# cmd 需要使用该管理员权限运行

# add alias to bash_aliases
echo "alias cygwin='C:/cygwin64/setup-x86_64.exe -q -P'" >> ~/.bash_aliases
source ~/.bash_aliases

# add bash_aliases to bashrc if missing
echo "source ~/.bash_aliases" >> ~/.profile

# win7 可能运行不了，因为缺少https证书 。使用镜像地址，请选在http 协议
setup-x86_64.exe --help
setup-x86_64.exe -q -s http://mirror.internode.on.net
setup-x86_64.exe -q -s http://mirrors.ustc.edu.cn/cygwin/

C:/cygwin/setup-x86_64.exe  --no-shortcuts  --quiet-mode  --disable-buggy-antivirus   --packages wget,tar,libtool,re2c,bison,gcc-g++,autoconf,automake,openssl
exit 0

./setup-x86_64.exe  --no-shortcuts  -q   -s http://mirrors.ustc.edu.cn/cygwin/   --packages     make


#  安装apt-cyg 参考： https://zhuanlan.zhihu.com/p/66930502

## apt-cyg必要环境
# base tar wget bzip2 gawk xz
# curl -Lo apt-cyg rawgit.com/transcode-open/apt-cyg/master/apt-cyg

lynx -source rawgit.com/transcode-open/apt-cyg/master/apt-cyg > apt-cyg
install apt-cyg /bin
apt-cyg --help

# apt-cyg mirror https://mirrors.ustc.edu.cn/cygwin/

apt-cyg install wget tar libtool re2c bison gcc-g++ autoconf automake openssl libpcre2-devel libssl-devel libcurl-devel libxml2-devel libxslt-devel libgmp-devel ImageMagick libpng-devel libjpeg-devel libfreetype-devel libwebp-devel libsqlite3-devel zlib-devel libbz2-devel libreadline-devel  libintl-devel libpq-devel libssh2-devel libidn2-devel gettext-devel coreutils


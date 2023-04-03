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

# msys2  参考文档
# https://mirror.tuna.tsinghua.edu.cn/help/msys2/


# 镜像下载地址 https://mirrors.tuna.tsinghua.edu.cn/msys2/

# https://github.com/msys2/setup-msys2

# 搜索包

pacman -Ss icu

# web 站点搜索包

# https://packages.msys2.org/search?t=pkg&q=zlib-devel

pacman -Sy --noconfirm  wget tar libtool re2c bison gcc autoconf automake openssl

pacman -Sy --noconfirm pcre2-devel openssl-devel libcurl-devel libxml2-devel libxslt-devel gmp-devel libsqlite-devel zlib-devel libbz2-devel \
           liblz4-devel liblzma-devel icu-devel libcares-devel libyaml-devel libzstd-devel brotli-devel libreadline-devel  libintl \
           libssh2-devel  libidn2-devel gettext-devel coreutils

pacman -Sy --noconfirm zip gzip lzip p7zip zlib bzip2 unzip

:<<'EOF'

# 这些包找不到

     ImageMagick libpng-devel libjpeg-devel libfreetype-devel libwebp-devel  \
     libzip-devel  libonig-devel  libsodium-devel  libMagick-devel     libpq-devel


     github action ci
     https://www.msys2.org/docs/ci/

EOF

pacman -Ss  zip

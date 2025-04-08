@echo off

:: cygwin site: https://cygwin.com/
:: start https://cygwin.com/setup-x86_64.exe

setlocal enableextensions enabledelayedexpansion

echo %~dp0
cd /d %~dp0
cd /d ..\..\..\..\

set "__PROJECT__=%cd%"
cd /d %__PROJECT__%\
echo %cd%



set "SITE=https://mirrors.kernel.org/sourceware/cygwin/"

:getopt
if /i "%1" equ "--mirror" (
	if /i "%2" equ "china" (
		set "SITE=https://mirrors.ustc.edu.cn/cygwin/"
	)
)
shift

if not (%1)==() goto getopt

set "OPTIONS= --quiet-mode --disable-buggy-antivirus --site  %SITE%  "
set "PACKAGES="

if defined GITHUB_ACTIONS (
	set "OPTIONS= %OPTIONS% --no-desktop --no-shortcuts --no-startmenu  "
)

:: package  separate with commas
set "PACKAGES=make,git,curl,wget,tar,libtool,bison,gcc-g++,autoconf,automake,openssl,libpcre2-devel,libssl-devel,libcurl-devel,libxml2-devel,libxslt-devel,libgmp-devel,ImageMagick,libpng-devel,libjpeg-devel,libfreetype-devel,libwebp-devel,libsqlite3-devel,zlib-devel,libbz2-devel,liblz4-devel,liblzma-devel,libzip-devel,libicu-devel,libonig-devel,libcares-devel,libsodium-devel,libyaml-devel,libMagick-devel,libzstd-devel,libbrotli-devel,libreadline-devel,libintl-devel,libpq-devel,libssh2-devel,libidn2-devel,gettext-devel,coreutils"
set "PACKAGES=%PACKAGES%,zip,unzip"
set "PACKAGES=%PACKAGES%,libpq5,libpq-devel"
set "PACKAGES=%PACKAGES%,libzstd-devel"
set "PACKAGES=%PACKAGES%,cygwin-devel,libnet6-devel"

set "OPTIONS=%OPTIONS% --packages %PACKAGES%"
echo %OPTIONS%

start /b /wait setup-x86_64.exe  %OPTIONS%

endlocal

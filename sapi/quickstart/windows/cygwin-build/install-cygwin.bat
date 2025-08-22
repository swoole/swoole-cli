@echo off

:: cygwin site: https://cygwin.com/
:: start https://cygwin.com/setup-x86_64.exe
:: search package https://cygwin.com/cgi-bin2/package-grep.cgi

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
set "PACKAGES=make,git,curl,wget,tar,libtool,bison,gcc-g++,autoconf,automake"
set "PACKAGES=%PACKAGES%,cmake,openssl,binutils"
set "PACKAGES=%PACKAGES%,libssl-devel,libcurl-devel,libxml2-devel,libxslt-devel"
set "PACKAGES=%PACKAGES%,libssh2-devel,libidn2-devel"
set "PACKAGES=%PACKAGES%,libgmp-devel,libsqlite3-devel,libpcre-devel,libpcre2-devel"
set "PACKAGES=%PACKAGES%,libiconv-devel"
set "PACKAGES=%PACKAGES%,libMagick-devel,ImageMagick,libpng-devel,libjpeg-devel,libfreetype-devel,libwebp-devel"
set "PACKAGES=%PACKAGES%,zlib-devel,libbz2-devel,liblz4-devel,liblzma-devel,libzip-devel"
set "PACKAGES=%PACKAGES%,libzstd-devel,libbrotli-devel"
set "PACKAGES=%PACKAGES%,zip,unzip,xz"
set "PACKAGES=%PACKAGES%,libreadline-devel,libicu-devel,libonig-devel,libcares-devel,libsodium-devel,libyaml-devel"
set "PACKAGES=%PACKAGES%,libintl-devel,gettext-devel"
set "PACKAGES=%PACKAGES%,libpq5,libpq-devel"
set "PACKAGES=%PACKAGES%,flex"
set "PACKAGES=%PACKAGES%,cygwin-devel,libnet6-devel"
set "PACKAGES=%PACKAGES%,libwrap-devel"
set "PACKAGES=%PACKAGES%,libedit-devel"


set "OPTIONS=%OPTIONS% --packages %PACKAGES%"
echo %OPTIONS%

start /b /wait setup-x86_64.exe  %OPTIONS%

endlocal

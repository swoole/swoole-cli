@echo off

setlocal enabledelayedexpansion
rem show current file location
echo %~dp0
cd /d %~dp0
cd /d ..\..\..\

set "__PROJECT__=%cd%"
echo %cd%
cd "%__PROJECT__%"

set "PATH=%__PROJECT__%\runtime\jom;%PATH%"

set CL=/MP

rem set RTLIBCFG=static
rem nmake   mode=static debug=false


:: nmake /E php.exe
:: exit /b

set "X_CUSTOM_LIBS="
set "X_CUSTOM_LIBS= %X_CUSTOM_LIBS% libzstd_a.lib"
set "X_CUSTOM_LIBS= %X_CUSTOM_LIBS% cares.lib psl.lib ngtcp2.lib ngtcp2_crypto_ossl.lib nghttp3.lib "
set "X_CUSTOM_LIBS= %X_CUSTOM_LIBS% pgport.lib pgcommon.lib ecpg.lib  ecpg_compat.lib    pgfeutils.lib  pgtypes.lib  "
set "X_CUSTOM_LIBS= %X_CUSTOM_LIBS% Secur32.lib Ws2_32.lib Crypt32.lib  libpq.lib "
set "X_CUSTOM_LIBS= %X_CUSTOM_LIBS% Secur32.lib Ws2_32.lib Crypt32.lib  libpq.lib "
set "X_CUSTOM_LIBS= %X_CUSTOM_LIBS% brotlicommon.lib brotlidec.lib brotlienc.lib"
set "X_CUSTOM_LIBS= %X_CUSTOM_LIBS% libsharpyuv.lib libwebpdecoder.lib libwebpdemux.lib libwebpmux.lib"
set "X_CUSTOM_LIBS= %X_CUSTOM_LIBS% avif_a.lib SvtAv1Enc.lib "
set "X_CUSTOM_LIBS= %X_CUSTOM_LIBS% odbc32.lib "
set "X_CUSTOM_LIBS= %X_CUSTOM_LIBS% libsqlite3_a.lib "


sed.exe -i.bak "s/sapi\\phpdbg\\phpdbg_parser\.c//" Makefile
sed.exe -i.bak "s/sapi\\phpdbg\\phpdbg_parser\.c sapi\\phpdbg\\phpdbg_lexer\.c//" Makefile

:: nmake /E /f Makefile  x-show-var
nmake  /E /f Makefile  x-release-static-php
:: jom.exe /E /f Makefile  x-release-static-php
:: nmake /E /n /f Makefile  x-release-static-php mode=static LIBS_CLI="%LIBS_CLI%"

rem debug
:: set "PATH=%PATH%;C:\Program Files (x86)\Windows Kits\10\Debuggers\x64\"
:: $env:Path += ";C:\Program Files (x86)\Windows Kits\10\Debuggers\x64"
:: cdb.exe -pn  "%__PROJECT__%\var\native-build\php-src\x64\Release_TS\php.exe"

.\x64\Release_TS\php.exe -v
.\x64\Release_TS\php.exe -m
:: dumpbin /DEPENDENTS ".\x64\Release\php.exe"

dumpbin /DEPENDENTS ".\x64\Release_TS\php.exe"

cd %__PROJECT__%
endlocal


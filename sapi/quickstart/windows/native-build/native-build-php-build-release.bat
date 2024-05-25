@echo off

setlocal
rem show current file location
echo %~dp0
cd /d %~dp0
cd /d ..\..\..\..\

set "__PROJECT__=%cd%"
echo %cd%
cd %__PROJECT__%\php-src\

set "INCLUDE=%INCLUDE%;%__PROJECT__%\openssl\include\;%__PROJECT__%\zlib\include"
set "LIB=%LIB%;%__PROJECT__%\openssl\lib\;%__PROJECT__%\zlib\lib"
set "LIBPATH=%LIBPATH%;%__PROJECT__%\openssl\lib\;%__PROJECT__%\zlib\lib\"

copy %__PROJECT__%\thirdparty\openssl\ms\applink.c  %__PROJECT__%\build\openssl\include\openssl\applink.c

set CL=/MP
rem set RTLIBCFG=static
rem nmake   mode=static debug=false
nmake

rem nmake install

cd %__PROJECT__%
endlocal


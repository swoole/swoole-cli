@echo off

echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
echo %cd%
cd %__PROJECT__%\php-src\

set "INCLUDE=%INCLUDE%;%__PROJECT__%\openssl\include\;%__PROJECT__%\zlib\include"
set "LIB=%LIB%;%__PROJECT__%\openssl\lib\;%__PROJECT__%\zlib\lib"
set "LIBPATH=%LIBPATH%;%__PROJECT__%\openssl\lib\;%__PROJECT__%\zlib\lib\"

set CL=/MP
nmake

cd %__PROJECT__%

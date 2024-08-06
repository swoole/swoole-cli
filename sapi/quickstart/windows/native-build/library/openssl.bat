@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  build


set "PATH=%__PROJECT__%\nasm\;C:\Strawberry\perl\bin;%PATH%"



cd /d %__PROJECT__%\thirdparty\openssl
dir
echo %cd%
perl -v

perl Configure VC-WIN64A threads no-shared  no-tests --release --prefix="%__PROJECT__%\build\openssl"  --openssldir="%__PROJECT__%\build\openssl\ssl"

set CL=/MP

rem document
rem openssl\Configurations\windows-makefile.tmpl

nmake install_sw

rem fix no found file " openssl/applink.c "
copy %__PROJECT__%\thirdparty\openssl\ms\applink.c  %__PROJECT__%\build\openssl\include\openssl\applink.c


cd /d %__PROJECT__%
endlocal

@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  "%__PROJECT__%\build\openssl"

if exist "%__PROJECT__%\build\openssl\.completed" goto :skip

set "PATH=%__PROJECT__%\runtime\nasm\;C:\Strawberry\perl\bin;%PATH%"


cd /d %__PROJECT__%\pool\lib\
7z.exe x -aoa -y   %__PROJECT__%\pool\lib\openssl-3.6.5.tar.gz

if  exist "%__PROJECT__%\thirdparty\openssl" rmdir /s /q "%__PROJECT__%\thirdparty\openssl"
mkdir "%__PROJECT__%\thirdparty\openssl"

7z.exe x -aoa -y  -o"%__PROJECT__%\thirdparty\openssl"  %__PROJECT__%\pool\lib\openssl-3.6.5.tar

cd /d %__PROJECT__%\thirdparty\openssl\openssl-3.6.5
dir
echo %cd%
perl -v


set "INCLUDE=%cd%\include\;%cd%\apps\include\;%INCLUDE%"
set CL=/MP
:: perl apps/progs.pl -H apps/openssl > apps/progs.h
perl Configure VC-WIN64A threads no-shared  no-legacy  no-tests  --release --prefix="%__PROJECT__%\build\openssl"  --openssldir="%__PROJECT__%\build\openssl\ssl"

nmake
nmake install_sw



rem document
rem openssl\Configurations\windows-makefile.tmpl
rem fix no found file " openssl/applink.c "
copy /Y %__PROJECT__%\thirdparty\openssl\openssl-3.6.5\ms\applink.c  %__PROJECT__%\build\openssl\include\openssl\applink.c
copy /Y %__PROJECT__%\build\openssl\lib\libcrypto.lib %__PROJECT__%\build\openssl\lib\libeay32st.lib

type nul > "%__PROJECT__%\build\openssl\.completed"

cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "openssl build completed ! "

endlocal

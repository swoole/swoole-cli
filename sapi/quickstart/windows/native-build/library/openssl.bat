@echo off

echo %~dp0
cd %~dp0
cd ..\..\..\..\..\

set __PROJECT__=%cd%
cd %__PROJECT__%
mkdir  build


set "PATH=%__PROJECT__%\nasm\;%PATH%"

cd %__PROJECT__%\thirdparty\openssl
dir
echo %cd%
perl -v

perl Configure VC-WIN64A threads no-shared  no-tests --release --prefix="%__PROJECT__%\build\openssl"  --openssldir="%__PROJECT__%\build\openssl\ssl"

nmake

nmake install


cd %__PROJECT__%

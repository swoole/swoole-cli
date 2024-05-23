@echo off

echo %~dp0
cd %~dp0
cd ..\..\..\..\..\

set __PROJECT__=%cd%
echo %cd%
cd %__PROJECT__%

mkdir -p  build


cd thirdparty\openssl
dir

perl Configure VC-WIN64A threads no-shared  no-tests --release --prefix="%__PROJECT__%\build\openssl"  --openssldir="%__PROJECT__%\build\openssl\ssl"

nmake

nmake install


cd %__PROJECT__%


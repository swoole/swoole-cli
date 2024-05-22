@echo off

cd thirdparty\openssl
dir


perl Configure VC-WIN64A no-shared

: nmake
nmake -f ms\nt.mak

: nmake install
make -f ms\nt.mak install

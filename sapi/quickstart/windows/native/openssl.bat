@echo off

cd thirdparty\openssl
dir


perl Configure VC-WIN64A no-shared

nmake
nmake install

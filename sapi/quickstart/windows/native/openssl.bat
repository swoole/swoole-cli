@echo off

cd thirdparty\openssl
dir


perl Configure VC-WIN64A no-shared --release --prefix=c:\php-cli\openssl --openssldir=c:\php-cli\openssl\ssl

nmake

nmake install


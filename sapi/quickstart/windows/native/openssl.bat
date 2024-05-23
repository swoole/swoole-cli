@echo off

cd thirdparty\openssl
dir


perl Configure VC-WIN64A no-shared no-docs --release --prefix=c:\php-cli\openssl --openssldir=c:\php-cli\openssl\ssl

nmake

nmake install


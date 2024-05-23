@echo off

cd thirdparty\openssl
dir


perl Configure VC-WIN64A threads no-shared  no-tests --release --prefix=c:\php-cli\openssl --openssldir=c:\php-cli\openssl\ssl

nmake

nmake install


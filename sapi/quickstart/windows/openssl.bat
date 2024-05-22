@echo off

mkdir thirdparty\openssl

tar --strip-components=1 -C thirdparty\openssl -xf pool\lib\openssl-3.1.4-quic1.tar.gz

cd thirdparty\openssl
dir


perl Configure VC-WIN64A

nmake
nmake install

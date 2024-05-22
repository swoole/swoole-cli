@echo off

cd thirdparty\curl
dir
mkdir build
cd build
cmake .. ^
-DCMAKE_BUILD_TYPE=Release  ^
-DBUILD_SHARED_LIBS=OFF  ^
-DBUILD_STATIC_LIBS=ON ^
-DSSL_ENABLED=ON ^
-DUSE_ZLIB=ON ^
-DUSE_OPENSSL=ON ^
-DUSE_WOLFSSL=OFF ^
-DUSE_GNUTLS=OFF ^
-DUSE_MBEDTLS=OFF ^
-DENABLE_WEBSOCKETS=OFF ^
-DCURL_USE_LIBSSH2=OFF


cmake --build . --config Release --target install

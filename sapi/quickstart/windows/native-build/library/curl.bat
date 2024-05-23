@echo off

echo %~dp0
cd %~dp0
cd ..\..\..\..\..\

set __PROJECT__=%cd%
echo %cd%
cd %__PROJECT__%

mkdir  build /S /Q



cd thirdparty\curl
dir

mkdir  build /S /Q
cd build
cmake .. ^
-DCMAKE_INSTALL_PREFIX="%__PROJECT__%\build\curl" ^
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
-DCURL_USE_LIBSSH2=OFF ^
-DCMAKE_PREFIX_PATH="C:/Program Files/OpenSSL/;C:/Program Files (x86)/zlib/"


cmake --build . --config Release --target install

cd %__PROJECT__%

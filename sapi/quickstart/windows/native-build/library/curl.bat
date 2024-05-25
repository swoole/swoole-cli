@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  build


set CMAKE_BUILD_PARALLEL_LEVEL=%NUMBER_OF_PROCESSORS%

cd thirdparty\curl
dir

mkdir  build
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
-DCURL_USE_LIBSSH2=ON ^
-DCMAKE_PREFIX_PATH="%__PROJECT__%\OpenSSL\;%__PROJECT__%\zlib\;%__PROJECT__%\libssh2\"


cmake --build . --config Release --target install


cd /d %__PROJECT__%
endlocal

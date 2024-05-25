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

cd thirdparty\libssh2
dir
mkdir  build
cd build
cmake .. ^
-DCMAKE_INSTALL_PREFIX="%__PROJECT__%\build\libssh2" ^
-DCMAKE_BUILD_TYPE=Release  ^
-DBUILD_SHARED_LIBS=OFF  ^
-DBUILD_STATIC_LIBS=ON ^
-DENABLE_ZLIB_COMPRESSION=ON  \
-DZLIB_ROOT="%__PROJECT__%\build\zlib\" ^
-DCLEAR_MEMORY=ON  \
-DENABLE_GEX_NEW=ON  \
-DENABLE_CRYPT_NONE=OFF  \
-DOpenSSL_ROOT="%__PROJECT__%\build\openssl\" ^
-DCRYPTO_BACKEND=OpenSSL \
-DBUILD_TESTING=OFF \
-DBUILD_EXAMPLES=OFF

cmake --build . --config Release --target install


cd /d %__PROJECT__%
endlocal

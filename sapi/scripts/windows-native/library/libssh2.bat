@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  "%__PROJECT__%\build\libssh2"

if exist "%__PROJECT__%\build\libssh2\.completed" goto :skip

set CMAKE_BUILD_PARALLEL_LEVEL=%NUMBER_OF_PROCESSORS%

cd /d %__PROJECT__%\pool\lib\
7z.exe x -aoa -y   %__PROJECT__%\pool\lib\libssh2-1.11.1.tar.gz

if  exist "%__PROJECT__%\thirdparty\libssh2" rmdir /s /q "%__PROJECT__%\thirdparty\libssh2"
mkdir "%__PROJECT__%\thirdparty\libssh2"

7z.exe x -aoa -y  -o"%__PROJECT__%\thirdparty\libssh2"  %__PROJECT__%\pool\lib\libssh2-1.11.1.tar
cd %__PROJECT__%\thirdparty\libssh2\libssh2-1.11.1\

dir

set "OPENSSL_PREFIX=%__PROJECT__%\build\openssl"
set "LIBZLIB_PREFIX=%__PROJECT__%\build\zlib"
set "LIBSSH2_PREFIX=%__PROJECT__%\build\libssh2"


mkdir  build
cd build
cmake .. ^
-DCMAKE_INSTALL_PREFIX="%LIBSSH2_PREFIX%" ^
-DCMAKE_POLICY_DEFAULT_CMP0074=NEW ^
-DCMAKE_POLICY_DEFAULT_CMP0091=NEW ^
-DCMAKE_MSVC_RUNTIME_LIBRARY=MultiThreaded ^
-DCMAKE_BUILD_TYPE=Release  ^
-DBUILD_SHARED_LIBS=OFF  ^
-DBUILD_STATIC_LIBS=ON ^
-DENABLE_ZLIB_COMPRESSION=ON ^
-DCLEAR_MEMORY=ON ^
-DENABLE_GEX_NEW=ON ^
-DENABLE_CRYPT_NONE=OFF ^
-DCRYPTO_BACKEND=OpenSSL ^
-DBUILD_TESTING=OFF ^
-DBUILD_EXAMPLES=OFF ^
-DOPENSSL_ROOT_DIR="%OPENSSL_PREFIX%" ^
-DZLIB_ROOT="%LIBZLIB_PREFIX%" ^
-DCMAKE_PREFIX_PATH="%LIBZLIB_PREFIX%;%OPENSSL_PREFIX%" ^
-DCMAKE_C_FLAGS="/MT /O2 /W3 /DNDEBUG" ^
-DCMAKE_VERBOSE_MAKEFILE=ON


cmake --build . --config Release --target install


type nul > "%__PROJECT__%\build\libssh2\.completed"

cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "libssh2 build completed ! "

endlocal

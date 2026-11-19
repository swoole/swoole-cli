@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  "%__PROJECT__%\build\ngtcp2"

if exist "%__PROJECT__%\build\ngtcp2\.completed" goto :skip

set CMAKE_BUILD_PARALLEL_LEVEL=%NUMBER_OF_PROCESSORS%

cd /d %__PROJECT__%\pool\lib\

if not exist "ngtcp2-1.25.0.tar.gz" curl.exe -fSLo ngtcp2-1.25.0.tar.gz  https://github.com/ngtcp2/ngtcp2/releases/download/v1.25.0/ngtcp2-1.25.0.tar.gz

7z.exe x -aoa -y   %__PROJECT__%\pool\lib\ngtcp2-1.25.0.tar.gz

if  exist "%__PROJECT__%\thirdparty\ngtcp2" rmdir /s /q "%__PROJECT__%\thirdparty\ngtcp2"
mkdir "%__PROJECT__%\thirdparty\ngtcp2"

7z.exe x -aoa -y  -o"%__PROJECT__%\thirdparty\ngtcp2"  %__PROJECT__%\pool\lib\ngtcp2-1.25.0.tar

cd /d %__PROJECT__%\thirdparty\ngtcp2\ngtcp2-1.25.0\

dir

set "OPENSSL_PREFIX=%__PROJECT__%\build\openssl"
set "NGTCP2_PREFIX=%__PROJECT__%\build\ngtcp2"
set "LIBBROTLI_PREFIX=%__PROJECT__%\build\brotli"


mkdir  build
cd build
cmake .. ^
-DCMAKE_INSTALL_PREFIX="%NGTCP2_PREFIX%" ^
-DCMAKE_BUILD_TYPE=Release  ^
-DCMAKE_MSVC_RUNTIME_LIBRARY=MultiThreaded ^
-DBUILD_SHARED_LIBS=OFF  ^
-DBUILD_STATIC_LIBS=ON ^
-DENABLE_STATIC_LIB=ON ^
-DENABLE_SHARED_LIB=OFF ^
-DENABLE_LIB_ONLY=ON ^
-DENABLE_OPENSSL=ON ^
-DBUILD_TESTING=OFF ^
-DCMAKE_PREFIX_PATH="%OPENSSL_PREFIX%;%LIBBROTLI_PREFIX%;" ^
-DCMAKE_C_FLAGS="/MT /O2 /W3 " ^
-DCMAKE_EXE_LINKER_FLAGS="/VERBOSE:LIB" ^
-DCMAKE_VERBOSE_MAKEFILE=ON


cmake --build . --config Release
cmake --build . --config Release --target install

type nul > "%__PROJECT__%\build\ngtcp2\.completed"

dumpbin /DIRECTIVES "%__PROJECT__%\build\ngtcp2\lib\ngtcp2.lib" | findstr /i "DEFAULTLIB"
dumpbin /DIRECTIVES "%__PROJECT__%\build\ngtcp2\lib\ngtcp2_crypto_ossl.lib" | findstr /i "DEFAULTLIB"

cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "ngtcp2 build completed ! "

endlocal

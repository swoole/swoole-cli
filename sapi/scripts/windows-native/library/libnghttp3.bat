@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  "%__PROJECT__%\build\libnghttp3"

if exist "%__PROJECT__%\build\libnghttp3\.completed" goto :skip

set CMAKE_BUILD_PARALLEL_LEVEL=%NUMBER_OF_PROCESSORS%

cd /d %__PROJECT__%\pool\lib\

if not exist "nghttp3-1.18.0.tar.gz" curl.exe -fSLo nghttp3-1.18.0.tar.gz  https://github.com/ngtcp2/nghttp3/releases/download/v1.18.0/nghttp3-1.18.0.tar.gz

7z.exe x -aoa -y   %__PROJECT__%\pool\lib\nghttp3-1.18.0.tar.gz

if  exist "%__PROJECT__%\thirdparty\libnghttp3" rmdir /s /q "%__PROJECT__%\thirdparty\libnghttp3"
mkdir "%__PROJECT__%\thirdparty\libnghttp3"

7z.exe x -aoa -y  -o"%__PROJECT__%\thirdparty\libnghttp3"  %__PROJECT__%\pool\lib\nghttp3-1.18.0.tar

cd /d %__PROJECT__%\thirdparty\libnghttp3\nghttp3-1.18.0\

dir

set "OPENSSL_PREFIX=%__PROJECT__%\build\openssl"
set "LIBNGHTTP3_PREFIX=%__PROJECT__%\build\libnghttp3"


mkdir  build
cd build
cmake .. ^
-DCMAKE_INSTALL_PREFIX="%LIBNGHTTP3_PREFIX%" ^
-DCMAKE_BUILD_TYPE=Release  ^
-DCMAKE_MSVC_RUNTIME_LIBRARY=MultiThreaded ^
-DBUILD_SHARED_LIBS=OFF  ^
-DBUILD_STATIC_LIBS=ON ^
-DENABLE_LIB_ONLY=ON ^
-DENABLE_STATIC_LIB=ON ^
-DENABLE_SHARED_LIB=OFF ^
-DENABLE_STATIC_CRT=ON ^
-DBUILD_TESTING=OFF ^
-DCMAKE_C_FLAGS="/MT /O2 /W3 " ^
-DCMAKE_EXE_LINKER_FLAGS="/VERBOSE:LIB" ^
-DCMAKE_VERBOSE_MAKEFILE=ON


cmake --build . --config Release
cmake --build . --config Release --target install

type nul > "%__PROJECT__%\build\libnghttp3\.completed"

dumpbin /DIRECTIVES "%__PROJECT__%\build\libnghttp3\lib\nghttp3.lib" | findstr /i "DEFAULTLIB"

cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "libnghttp3 build completed ! "

endlocal

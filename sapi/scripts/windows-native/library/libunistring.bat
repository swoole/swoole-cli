@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  "%__PROJECT__%\build\libunistring"

if exist "%__PROJECT__%\build\libunistring\.completed" goto :skip

set CMAKE_BUILD_PARALLEL_LEVEL=%NUMBER_OF_PROCESSORS%


cd /d %__PROJECT__%\pool\lib\
7z.exe x -aoa -y   %__PROJECT__%\pool\lib\libunistring-1.1.tar.gz

if  exist "%__PROJECT__%\thirdparty\libunistring" rmdir /s /q "%__PROJECT__%\thirdparty\libunistring"
mkdir "%__PROJECT__%\thirdparty\libunistring"

7z.exe x -aoa -y  -o"%__PROJECT__%\thirdparty\libunistring"  %__PROJECT__%\pool\lib\libunistring-1.1.tar
cd %__PROJECT__%\thirdparty\libunistring\libunistring-1.1


exit 0
set "OPENSSL_PREFIX=%__PROJECT__%\build\openssl"
set "ZLIB_PREFIX=%__PROJECT__%\build\zlib"
set "LIBXML2_PREFIX=%__PROJECT__%\build"
set "LIBBROTLI_PREFIX=%__PROJECT__%\build\brotli"
set "LIBZSTD_PREFIX=%__PROJECT__%\build\libzstd"

dir
mkdir  build-dir
cd build-dir
cmake .. ^
-DCMAKE_INSTALL_PREFIX="%__PROJECT__%\build\libnghttp2" ^
-DCMAKE_BUILD_TYPE=Release  ^
-DCMAKE_MSVC_RUNTIME_LIBRARY=MultiThreaded ^
-DBUILD_SHARED_LIBS=OFF  ^
-DBUILD_STATIC_LIBS=ON ^
-DENABLE_STATIC_CRT=LIBCMT ^
-DENABLE_LIB_ONLY=ON ^
-DENABLE_DOC=OFF ^
-DENABLE_APP=OFF ^
-DENABLE_HTTP3=OFF ^
-DENABLE_FAILMALLOC=OFF ^
-DBUILD_TESTING=OFF ^
-DOPENSSL_ROOT_DIR=%OPENSSL_PREFIX% ^
-DWITH_LIBXML2=ON ^
-DLIBXML2_LIBRARY="%LIBXML2_PREFIX%\libxml2_a.lib" ^
-DLIBXML2_INCLUDE_DIR="%LIBXML2_PREFIX%" ^
-DCMAKE_PREFIX_PATH="%OPENSSL_PREFIX%;%ZLIB_PREFIX%;%LIBXML2_PREFIX%;" ^
-DCMAKE_DISABLE_FIND_PACKAGE_Libngtcp2=ON ^
-DCMAKE_DISABLE_FIND_PACKAGE_Systemd=ON ^
-DCMAKE_DISABLE_FIND_PACKAGE_Libngtcp2=ON ^
-DCMAKE_DISABLE_FIND_PACKAGE_Libnghttp3=ON ^
-DCMAKE_DISABLE_FIND_PACKAGE_Jansson=ON ^
-DCMAKE_DISABLE_FIND_PACKAGE_Jemalloc=ON ^
-DCMAKE_DISABLE_FIND_PACKAGE_Libevent=ON ^
-DCMAKE_DISABLE_FIND_PACKAGE_Python3=ON


cmake --build . --config Release --target install

type nul > "%__PROJECT__%\build\libunistring\.completed"

cd /d %__PROJECT__%

:skip
echo "skip"
echo "libunistring build completed ! "

endlocal

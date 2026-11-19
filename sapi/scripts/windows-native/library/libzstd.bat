@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  "%__PROJECT__%\build\libzstd"
if exist "%__PROJECT__%\build\libzstd\.completed" goto :skip

set CMAKE_BUILD_PARALLEL_LEVEL=%NUMBER_OF_PROCESSORS%

cd /d %__PROJECT__%\pool\lib\
7z.exe x -aoa -y   %__PROJECT__%\pool\lib\zstd-1.5.2.tar.gz

if  exist "%__PROJECT__%\thirdparty\libzstd" rmdir /s /q "%__PROJECT__%\thirdparty\libzstd"
mkdir "%__PROJECT__%\thirdparty\libzstd"

7z.exe x -aoa -y  -o"%__PROJECT__%\thirdparty\libzstd"  %__PROJECT__%\pool\lib\zstd-1.5.2.tar
cd %__PROJECT__%\thirdparty\libzstd\zstd-1.5.2\

cd build\cmake\
dir

set "LIBLZ4_PREFIX=%__PROJECT__%\build\liblz4"
set "LIBLZMA_PREFIX=%__PROJECT__%\build\liblzma"
set "LIBZLIB_PREFIX=%__PROJECT__%\build\zlib"

mkdir  build-dir
cd build-dir
cmake .. ^
-DCMAKE_INSTALL_PREFIX="%__PROJECT__%\build\libzstd" ^
-DCMAKE_BUILD_TYPE=Release  ^
-DBUILD_SHARED_LIBS=OFF  ^
-DZSTD_BUILD_SHARED=OFF ^
-DBUILD_STATIC_LIBS=ON ^
-DZSTD_BUILD_STATIC=ON ^
-DCMAKE_MSVC_RUNTIME_LIBRARY=MultiThreaded ^
-DZSTD_BUILD_CONTRIB=OFF ^
-DZSTD_BUILD_PROGRAMS=OFF ^
-DZSTD_BUILD_TESTS=OFF ^
-DZSTD_LEGACY_SUPPORT=ON ^
-DZSTD_MULTITHREAD_SUPPORT=ON ^
-DCMAKE_C_FLAGS="/MT /O2 /W3 /DNDEBUG " ^
-DCMAKE_C_FLAGS_RELEASE="/MT /O2 /W3 /DNDEBUG" ^
-DCMAKE_VERBOSE_MAKEFILE=ON ^
-DCMAKE_PREFIX_PATH="%LIBLZ4_PREFIX%;%LIBLZMA_PREFIX%;%LIBZLIB_PREFIX%;"

cmake --build . --config Release --target install

del "%__PROJECT__%\build\libzstd\bin\zstd.dll"
del "%__PROJECT__%\build\libzstd\lib\zstd.lib"

copy /Y "%__PROJECT__%\build\libzstd\lib\zstd_static.lib" "%__PROJECT__%\build\libzstd\lib\libzstd_a.lib"
copy /Y "%__PROJECT__%\build\libzstd\lib\zstd_static.lib" "%__PROJECT__%\build\libzstd\lib\libzstd.lib"

type nul > "%__PROJECT__%\build\libzstd\.completed"

dumpbin /DIRECTIVES "%__PROJECT__%\build\libzstd\lib\zstd_static.lib" | findstr /i "DEFAULTLIB"
dumpbin /SYMBOLS "%__PROJECT__%\build\libzstd\lib\zstd_static.lib" | findstr "ZSTD_" | findstr "imp"

findstr /i "dllimport" "%__PROJECT__%\build\libzstd\include\zstd.h"

cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "libzstd build completed ! "

endlocal

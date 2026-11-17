@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  "%__PROJECT__%\build\liblzma"

if exist "%__PROJECT__%\build\liblzma\.completed" goto :skip


set CMAKE_BUILD_PARALLEL_LEVEL=%NUMBER_OF_PROCESSORS%
cd /d %__PROJECT__%\pool\lib\
7z.exe x -aoa -y   %__PROJECT__%\pool\lib\xz-5.4.1.tar.gz

if  exist "%__PROJECT__%\thirdparty\liblzma" rmdir /s /q "%__PROJECT__%\thirdparty\liblzma"
mkdir "%__PROJECT__%\thirdparty\liblzmal"

if exist "%__PROJECT__%\build\liblzma\.completed" goto :skip

7z.exe x -aoa -y  -o"%__PROJECT__%\thirdparty\liblzma"  %__PROJECT__%\pool\lib\xz-5.4.1.tar
cd %__PROJECT__%\thirdparty\liblzma\xz-5.4.1\

dir
mkdir  build-dir
cd build-dir
cmake .. ^
-DCMAKE_INSTALL_PREFIX="%__PROJECT__%\build\liblzma" ^
-DCMAKE_BUILD_TYPE=Release  ^
-DBUILD_SHARED_LIBS=OFF  ^
-DBUILD_STATIC_LIBS=ON ^
-DCMAKE_MSVC_RUNTIME_LIBRARY=MultiThreaded ^
-DCMAKE_C_FLAGS="/MT /O2 /W3 /DNDEBUG" ^
-DCMAKE_C_FLAGS_RELEASE="/MT /O2 /W3 /DNDEBUG" ^
-DCMAKE_VERBOSE_MAKEFILE=ON

cmake --build . --config Release --target install

copy /Y "%__PROJECT__%\build\liblzma\lib\liblzma.lib" "%__PROJECT__%\build\liblzma\lib\liblzma_a.lib"
del "%__PROJECT__%\build\liblzma\lib\liblzma.lib"

type nul > "%__PROJECT__%\build\liblzma\.completed"

dumpbin /DIRECTIVES "%__PROJECT__%\build\liblzma\lib\liblzma_a.lib" |  findstr /i "DEFAULTLIB"

findstr /i "dllimport" "%__PROJECT__%\build\liblzma\include\lzma.h"

cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "liblzma build completed ! "

endlocal

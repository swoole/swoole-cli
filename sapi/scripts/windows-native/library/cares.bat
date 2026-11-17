@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  "%__PROJECT__%\build\cares"

if exist "%__PROJECT__%\build\cares\.completed" goto :skip


set CMAKE_BUILD_PARALLEL_LEVEL=%NUMBER_OF_PROCESSORS%

cd /d %__PROJECT__%\pool\lib\
7z.exe x -aoa -y   "%__PROJECT__%\pool\lib\c-ares-1.24.0.tar.gz"

if  exist "%__PROJECT__%\thirdparty\cares" rmdir /s /q "%__PROJECT__%\thirdparty\cares"
mkdir "%__PROJECT__%\thirdparty\cares"

7z.exe x -aoa -y  -o"%__PROJECT__%\thirdparty\cares"  "%__PROJECT__%\pool\lib\c-ares-1.24.0.tar"

cd /d "%__PROJECT__%\thirdparty\cares\c-ares-1.24.0"


dir

set "LIBCARES_PREFIX=%__PROJECT__%\build\cares"


mkdir  build
cd build
cmake .. ^
-DCMAKE_INSTALL_PREFIX="%LIBCARES_PREFIX%" ^
-DCMAKE_BUILD_TYPE=Release  ^
-DCMAKE_MSVC_RUNTIME_LIBRARY=MultiThreaded ^
-DBUILD_SHARED_LIBS=OFF  ^
-DBUILD_STATIC_LIBS=ON ^
-DCARES_MSVC_STATIC_RUNTIME=ON ^
-DCARES_STATIC=ON ^
-DCARES_SHARED=OFF ^
-DCARES_STATIC_PIC=ON ^
-DCARES_BUILD_TESTS=OFF ^
-DCARES_BUILD_CONTAINER_TESTS=OFF ^
-DCARES_BUILD_TOOLS=OFF ^
-DCMAKE_C_FLAGS="/MT /O2 /W3  " ^
-DCMAKE_EXE_LINKER_FLAGS="/VERBOSE:LIB" ^
-DCMAKE_VERBOSE_MAKEFILE=ON

cmake --build . --config Release
cmake --build . --config Release --target install

type nul > "%__PROJECT__%\build\cares\.completed"

dumpbin /DIRECTIVES "%LIBCARES_PREFIX%\lib\cares.lib" | findstr /i "DEFAULTLIB"

cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "cares build completed ! "

endlocal


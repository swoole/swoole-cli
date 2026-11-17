@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  "%__PROJECT__%\build\libidn2"

if exist "%__PROJECT__%\build\libidn2\.completed" goto :skip

set CMAKE_BUILD_PARALLEL_LEVEL=%NUMBER_OF_PROCESSORS%


cd /d %__PROJECT__%\pool\lib\
7z.exe x -aoa -y   %__PROJECT__%\pool\lib\libidn2-2.3.8.tar.gz

if  exist "%__PROJECT__%\thirdparty\libidn2" rmdir /s /q "%__PROJECT__%\thirdparty\libidn2"
mkdir "%__PROJECT__%\thirdparty\libidn2"

7z.exe x -aoa -y  -o"%__PROJECT__%\thirdparty\libidn2"  %__PROJECT__%\pool\lib\libidn2-2.3.8.tar
cd %__PROJECT__%\thirdparty\libidn2\libidn2-2.3.8\

set "LIBIDN2_PREFIX=%__PROJECT__%\build\libidn2"

dir
mkdir  build-dir
cd build-dir
cmake .. ^
-DCMAKE_INSTALL_PREFIX="%LIBIDN2_PREFIX%" ^
-DCMAKE_BUILD_TYPE=Release  ^
-DCMAKE_MSVC_RUNTIME_LIBRARY=MultiThreaded ^
-DBUILD_SHARED_LIBS=OFF  ^
-DBUILD_STATIC_LIBS=ON ^
-DENABLE_STATIC_CRT=LIBCMT ^
-DENABLE_LIB_ONLY=ON ^


cmake --build . --config Release
cmake --build . --config Release --target install

type nul > "%__PROJECT__%\build\libidn2\.completed"

cd /d %__PROJECT__%

:skip
echo "skip"
echo "libidn2 build completed ! "

endlocal

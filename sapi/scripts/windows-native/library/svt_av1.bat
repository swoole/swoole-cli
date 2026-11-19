@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  %__PROJECT__%\build\svt_av1


if exist "%__PROJECT__%\build\svt_av1\.completed" goto :skip


mkdir %__PROJECT__%\var\native-build
cd /d %__PROJECT__%\var\native-build


set CMAKE_BUILD_PARALLEL_LEVEL=%NUMBER_OF_PROCESSORS%


cd /d %__PROJECT__%\pool\lib\
7z.exe x -aoa -y   %__PROJECT__%\pool\lib\SVT-AV1-v2.2.1.tar.gz

if  exist "%__PROJECT__%\thirdparty\svt_av1" rmdir /s /q "%__PROJECT__%\thirdparty\svt_av1"
mkdir "%__PROJECT__%\thirdparty\svt_av1"

7z.exe x -aoa -y  -o"%__PROJECT__%\thirdparty\svt_av1"  %__PROJECT__%\pool\lib\SVT-AV1-v2.2.1.tar
cd %__PROJECT__%\thirdparty\svt_av1\SVT-AV1-v2.2.1

dir

set "PATH=%__PROJECT__%\runtime\nasm;%PATH%;"

if exist "build_static" (
    rmdir /S /Q build_static
)

rem docs: https://github.com/AOMediaCodec/SVT-AV1

mkdir  build_static
cmake -B build_static -S . ^
	-DCMAKE_INSTALL_PREFIX="%__PROJECT__%\build\svt_av1" ^
	-DCMAKE_BUILD_TYPE=Release  ^
	-DBUILD_SHARED_LIBS=OFF  ^
	-DBUILD_STATIC_LIBS=ON  ^
	-DCMAKE_MSVC_RUNTIME_LIBRARY=MultiThreaded ^
	-DCMAKE_C_FLAGS="/MT /O2 /W3 /DNDEBUG" ^
	-DCMAKE_C_FLAGS_RELEASE="/MT /O2 /W3 /DNDEBUG" ^
	-DCMAKE_VERBOSE_MAKEFILE=ON

cmake --build build_static --config Release --target install

type nul > "%__PROJECT__%\build\svt_av1\.completed"

cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "svt_av1 build completed ! "

exit /b 0

cd /d %__PROJECT__%

endlocal

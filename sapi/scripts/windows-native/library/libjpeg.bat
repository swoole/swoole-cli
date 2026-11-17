@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  %__PROJECT__%\build\libjpeg


if exist "%__PROJECT__%\build\libjpeg\.completed" goto :skip


mkdir %__PROJECT__%\var\native-build
cd /d %__PROJECT__%\var\native-build


set CMAKE_BUILD_PARALLEL_LEVEL=%NUMBER_OF_PROCESSORS%


cd /d %__PROJECT__%\pool\lib\
7z.exe x -aoa -y   %__PROJECT__%\pool\lib\libjpeg-turbo-2.1.2.tar.gz

if  exist "%__PROJECT__%\thirdparty\libjpeg" rmdir /s /q "%__PROJECT__%\thirdparty\libjpeg"
mkdir "%__PROJECT__%\thirdparty\libjpeg"

7z.exe x -aoa -y  -o"%__PROJECT__%\thirdparty\libjpeg"  %__PROJECT__%\pool\lib\libjpeg-turbo-2.1.2.tar
cd %__PROJECT__%\thirdparty\libjpeg\libjpeg-turbo-2.1.2

dir

set "PATH=%__PROJECT__%\runtime\nasm;%PATH%;"

if exist "build_static" (
    rmdir /S /Q build_static
)

mkdir  build_static
cmake -B build_static -S . ^
	-DCMAKE_INSTALL_PREFIX="%__PROJECT__%\build\libjpeg" ^
	-DCMAKE_INSTALL_LIBDIR="%__PROJECT__%\build\libjpeg\lib" ^
	-DCMAKE_INSTALL_INCLUDEDIR="%__PROJECT__%\build\libjpeg\include" ^
	-DCMAKE_BUILD_TYPE=Release  ^
	-DBUILD_SHARED_LIBS=OFF  ^
	-DBUILD_STATIC_LIBS=ON  ^
	-DENABLE_SHARED=OFF  ^
	-DENABLE_STATIC=ON ^
	-DCMAKE_MSVC_RUNTIME_LIBRARY=MultiThreaded ^
	-DCMAKE_C_FLAGS="/MT /O2 /W3 /DNDEBUG" ^
	-DCMAKE_C_FLAGS_RELEASE="/MT /O2 /W3 /DNDEBUG" ^
	-DCMAKE_VERBOSE_MAKEFILE=ON



cmake --build build_static --config Release --target install

copy /Y "%__PROJECT__%\build\libjpeg\lib\jpeg-static.lib" "%__PROJECT__%\build\libjpeg\lib\libjpeg.lib"
copy /Y "%__PROJECT__%\build\libjpeg\lib\jpeg-static.lib" "%__PROJECT__%\build\libjpeg\lib\libjpeg_a.lib"


type nul > "%__PROJECT__%\build\libjpeg\.completed"

cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "libjpeg build completed ! "

exit /b 0

cd /d %__PROJECT__%

endlocal

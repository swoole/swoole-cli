@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

rem
rem cmd /c var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t .\sapi\scripts\windows-native\library\zlib.bat
rem
set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  "%__PROJECT__%\build\zlib"

if exist "%__PROJECT__%\build\zlib\.completed" goto :skip

set CMAKE_BUILD_PARALLEL_LEVEL=%NUMBER_OF_PROCESSORS%


cd /d %__PROJECT__%\pool\lib\
7z.exe x -aoa -y   %__PROJECT__%\pool\lib\zlib-v1.3.1.tar.gz

if  exist "%__PROJECT__%\thirdparty\zlib" rmdir /s /q "%__PROJECT__%\thirdparty\zlib"
mkdir "%__PROJECT__%\thirdparty\zlib"

7z.exe x -aoa -y  -o"%__PROJECT__%\thirdparty\zlib"  %__PROJECT__%\pool\lib\zlib-v1.3.1.tar
cd %__PROJECT__%\thirdparty\zlib\zlib-1.3.1\

dir

mkdir build

cd build
dir
echo %cd%

cmake .. ^
-DCMAKE_INSTALL_PREFIX="%__PROJECT__%\build\zlib" ^
-DCMAKE_BUILD_TYPE=Release  ^
-DBUILD_SHARED_LIBS=OFF  ^
-DBUILD_STATIC_LIBS=ON ^
-DCMAKE_MSVC_RUNTIME_LIBRARY=MultiThreaded

cmake --build . --config Release --target install

del "%__PROJECT__%\build\zlib\lib\zlib.lib"
del "%__PROJECT__%\build\zlib\bin\zlib.dll"
copy /Y "%__PROJECT__%\build\zlib\lib\zlibstatic.lib" "%__PROJECT__%\build\zlib\lib\zlib.lib"
copy /Y "%__PROJECT__%\build\zlib\lib\zlibstatic.lib" "%__PROJECT__%\build\zlib\lib\zlib_a.lib"
dir "%__PROJECT__%\build\zlib\lib\"

type nul > "%__PROJECT__%\build\zlib\.completed"

cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "zlib build completed ! "

endlocal

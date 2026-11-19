@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  "%__PROJECT__%\build\oniguruma"

if exist "%__PROJECT__%\build\oniguruma\.completed" goto :skip

set CMAKE_BUILD_PARALLEL_LEVEL=%NUMBER_OF_PROCESSORS%


cd /d %__PROJECT__%\pool\lib\
7z.exe x -aoa -y   %__PROJECT__%\pool\lib\oniguruma-v6.9.9.tar.gz

if  exist "%__PROJECT__%\thirdparty\oniguruma" rmdir /s /q "%__PROJECT__%\thirdparty\oniguruma"
mkdir "%__PROJECT__%\thirdparty\oniguruma"

7z.exe x -aoa -y  -o"%__PROJECT__%\thirdparty\oniguruma"  %__PROJECT__%\pool\lib\oniguruma-v6.9.9.tar
cd %__PROJECT__%\thirdparty\oniguruma\oniguruma-6.9.9\

dir

set "ONIGURUMA_PREFIX=%__PROJECT__%\build\oniguruma"

mkdir  build-dir
cd build-dir
cmake .. ^
-DCMAKE_INSTALL_PREFIX="%ONIGURUMA_PREFIX%" ^
-DCMAKE_BUILD_TYPE=Release  ^
-DBUILD_SHARED_LIBS=OFF  ^
-DBUILD_STATIC_LIBS=ON  ^
-DCMAKE_MSVC_RUNTIME_LIBRARY=MultiThreaded ^
-DINSTALL_DOCUMENTATION=OFF  ^
-DINSTALL_EXAMPLES=OFF ^
-DBUILD_TEST=OFF ^
-DMSVC_STATIC_RUNTIME=ON ^
-DCMAKE_C_FLAGS="/MT /O2 /W3 /DNDEBUG" ^
-DCMAKE_VERBOSE_MAKEFILE=ON

cmake --build . --config Release
cmake --build . --config Release --target install


copy /Y "%ONIGURUMA_PREFIX%\lib\onig.lib" "%ONIGURUMA_PREFIX%\lib\libonig_a.lib"
copy /Y "%ONIGURUMA_PREFIX%\lib\onig.lib" "%ONIGURUMA_PREFIX%\lib\onig_a.lib"

type nul > "%__PROJECT__%\build\oniguruma\.completed"

cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "oniguruma build completed ! "

endlocal

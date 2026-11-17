@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  %__PROJECT__%\build\libpng


if exist "%__PROJECT__%\build\libpng\.completed" goto :skip


mkdir %__PROJECT__%\var\native-build
cd /d %__PROJECT__%\var\native-build


set CMAKE_BUILD_PARALLEL_LEVEL=%NUMBER_OF_PROCESSORS%


cd /d %__PROJECT__%\pool\lib\
7z.exe x -aoa -y   %__PROJECT__%\pool\lib\libpng-1.6.43.tar.gz

if  exist "%__PROJECT__%\thirdparty\libpng" rmdir /s /q "%__PROJECT__%\thirdparty\libpng"
mkdir "%__PROJECT__%\thirdparty\libpng"

7z.exe x -aoa -y  -o"%__PROJECT__%\thirdparty\libpng"  %__PROJECT__%\pool\lib\libpng-1.6.43.tar
cd %__PROJECT__%\thirdparty\libpng\libpng-1.6.43

dir



if exist "build_static" (
    rmdir /S /Q build_static
)

mkdir  build_static
cmake -B build_static -S . ^
  -DCMAKE_INSTALL_PREFIX="%__PROJECT__%\build\libpng" ^
  -DCMAKE_BUILD_TYPE=Release  ^
  -DBUILD_SHARED_LIBS=OFF  ^
  -DBUILD_STATIC_LIBS=ON  ^
  -DCMAKE_MSVC_RUNTIME_LIBRARY=MultiThreaded ^
  -DPNG_SHARED=OFF  ^
  -DPNG_STATIC=ON  ^
  -DPNG_TESTS=OFF ^
  -DCMAKE_PREFIX_PATH="%__PROJECT__%\build\zlib;" ^
  -DCMAKE_C_FLAGS="/MT /O2 /W3 /DNDEBUG" ^
  -DCMAKE_C_FLAGS_RELEASE="/MT /O2 /W3 /DNDEBUG" ^
  -DCMAKE_VERBOSE_MAKEFILE=ON

cmake --build build_static --config Release --target install


copy /Y "%__PROJECT__%\build\libpng\lib\libpng16_static.lib" "%__PROJECT__%\build\libpng\lib\libpng.lib"
copy /Y "%__PROJECT__%\build\libpng\lib\libpng16_static.lib" "%__PROJECT__%\build\libpng\lib\libpng_a.lib"
rem  Compatible with PHP build detection
xcopy "%__PROJECT__%\build\libpng\include" "%__PROJECT__%\build\include" /E /I /H /Y


if exist "%__PROJECT__%\build\libpng\lib\libpng_a.lib" (
    type nul > "%__PROJECT__%\build\libpng\.completed"
)
cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "libpng build completed ! "

cd /d %__PROJECT__%

endlocal

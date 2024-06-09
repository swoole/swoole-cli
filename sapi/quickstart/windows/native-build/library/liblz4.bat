@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  build

set CMAKE_BUILD_PARALLEL_LEVEL=%NUMBER_OF_PROCESSORS%

cd thirdparty\liblz4\build\cmake\
dir
mkdir  build-dir
cd build-dir
cmake .. ^
-DCMAKE_INSTALL_PREFIX="%__PROJECT__%\build\liblz4" ^
-DCMAKE_BUILD_TYPE=Release  ^
-DBUILD_SHARED_LIBS=OFF  ^
-DBUILD_STATIC_LIBS=ON  ^
-DLZ4_POSITION_INDEPENDENT_LIB=ON  ^
-DLZ4_BUILD_LEGACY_LZ4C=ON  ^
-DLZ4_BUILD_CLI=ON

cmake --build . --config Release --target install


cd /d %__PROJECT__%
endlocal

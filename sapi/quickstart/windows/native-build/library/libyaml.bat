@echo off

echo %~dp0
cd %~dp0
cd ..\..\..\..\..\

set __PROJECT__=%cd%
echo %cd%
cd %__PROJECT__%

mkdir build

cd thirdparty\libyaml
dir
mkdir -p  build
cd build
cmake .. ^
-DCMAKE_INSTALL_PREFIX="%__PROJECT__%\build\libyaml" ^
-DCMAKE_BUILD_TYPE=Release  ^
-DBUILD_SHARED_LIBS=OFF  ^
-DBUILD_STATIC_LIBS=ON

cmake --build . --config Release --target install

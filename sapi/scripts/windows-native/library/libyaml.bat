@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  build

set CMAKE_BUILD_PARALLEL_LEVEL=%NUMBER_OF_PROCESSORS%

cd /d %__PROJECT__%\pool\lib\
7z.exe x -aoa -y   %__PROJECT__%\pool\lib\yaml-0.2.5.tar.gz

if  exist "%__PROJECT__%\thirdparty\libyaml" rmdir /s /q "%__PROJECT__%\thirdparty\libyaml"
mkdir "%__PROJECT__%\thirdparty\libyaml"

7z.exe x -aoa -y  -o"%__PROJECT__%\thirdparty\libyaml"  %__PROJECT__%\pool\lib\yaml-0.2.5.tar
cd %__PROJECT__%\thirdparty\libyaml\yaml-0.2.5\

dir
mkdir  build
cd build
cmake .. ^
-DCMAKE_INSTALL_PREFIX="%__PROJECT__%\build\libyaml" ^
-DCMAKE_BUILD_TYPE=Release  ^
-DBUILD_SHARED_LIBS=OFF  ^
-DBUILD_STATIC_LIBS=ON

cmake --build . --config Release --target install


cd /d %__PROJECT__%
endlocal

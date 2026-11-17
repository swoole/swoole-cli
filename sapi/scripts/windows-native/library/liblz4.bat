@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  "%__PROJECT__%\build\liblz4"
if exist "%__PROJECT__%\build\liblz4\.completed" goto :skip

set CMAKE_BUILD_PARALLEL_LEVEL=%NUMBER_OF_PROCESSORS%
cd /d %__PROJECT__%\pool\lib\
7z.exe x -aoa -y   %__PROJECT__%\pool\lib\lz4-v1.9.4.tar.gz

if  exist "%__PROJECT__%\thirdparty\liblz4" rmdir /s /q "%__PROJECT__%\thirdparty\liblz4"
mkdir "%__PROJECT__%\thirdparty\liblz4"

7z.exe x -aoa -y  -o"%__PROJECT__%\thirdparty\liblz4"  %__PROJECT__%\pool\lib\lz4-v1.9.4.tar
cd %__PROJECT__%\thirdparty\liblz4\lz4-1.9.4\

cd build\cmake\
dir
mkdir  build-dir
cd build-dir
cmake -S .. -B . ^
-DCMAKE_INSTALL_PREFIX="%__PROJECT__%\build\liblz4" ^
-DCMAKE_BUILD_TYPE=Release  ^
-DBUILD_SHARED_LIBS=OFF  ^
-DBUILD_STATIC_LIBS=ON  ^
-DCMAKE_MSVC_RUNTIME_LIBRARY=MultiThreaded ^
-DLZ4_POSITION_INDEPENDENT_LIB=ON  ^
-DLZ4_BUILD_LEGACY_LZ4C=ON  ^
-DLZ4_BUILD_CLI=ON ^
-DCMAKE_C_FLAGS="/MT /O2 /W3 /DNDEBUG" ^
-DCMAKE_C_FLAGS_RELEASE="/MT /O2 /W3 /DNDEBUG"

cmake --build . --config Release --target install

type nul > "%__PROJECT__%\build\liblz4\.completed"

cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "liblz4 build completed ! "

endlocal

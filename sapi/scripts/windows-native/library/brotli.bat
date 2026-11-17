@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  "%__PROJECT__%\build\brotli"

if exist "%__PROJECT__%\build\brotli\.completed" goto :skip

set CMAKE_BUILD_PARALLEL_LEVEL=%NUMBER_OF_PROCESSORS%


cd /d %__PROJECT__%\pool\lib\
7z.exe x -aoa -y   %__PROJECT__%\pool\lib\brotli-1.0.9.tar.gz

if  exist "%__PROJECT__%\thirdparty\brotli" rmdir /s /q "%__PROJECT__%\thirdparty\brotli"
mkdir "%__PROJECT__%\thirdparty\brotli"

7z.exe x -aoa -y  -o"%__PROJECT__%\thirdparty\brotli"  %__PROJECT__%\pool\lib\brotli-1.0.9.tar
cd %__PROJECT__%\thirdparty\brotli\brotli-1.0.9\

dir


mkdir  build-dir
cd build-dir
cmake .. ^
-DCMAKE_INSTALL_PREFIX="%__PROJECT__%\build\brotli" ^
-DCMAKE_BUILD_TYPE=Release  ^
-DBUILD_SHARED_LIBS=OFF  ^
-DBUILD_STATIC_LIBS=ON  ^
-DCMAKE_MSVC_RUNTIME_LIBRARY=MultiThreaded ^
-DBROTLI_DISABLE_TESTS=OFF  ^
-DBROTLI_BUNDLED_MODE=OFF ^
-DCMAKE_C_FLAGS="/MT /O2 /W3 /DNDEBUG" ^
-DCMAKE_C_FLAGS_RELEASE="/MT /O2 /W3 /DNDEBUG"

cmake --build . --config Release --target install


del "%__PROJECT__%\build\brotli\bin\brotli.exe"
del "%__PROJECT__%\build\brotli\bin\brotlienc.dll"
del "%__PROJECT__%\build\brotli\bin\brotlidec.dll"
del "%__PROJECT__%\build\brotli\bin\brotlicommon.dll"
del "%__PROJECT__%\build\brotli\lib\brotlienc.lib"
del "%__PROJECT__%\build\brotli\lib\brotlidec.lib"
del "%__PROJECT__%\build\brotli\lib\brotlicommon.lib"

copy /Y "%__PROJECT__%\build\brotli\lib\brotlienc-static.lib" "%__PROJECT__%\build\brotli\lib\brotlienc.lib"
copy /Y "%__PROJECT__%\build\brotli\lib\brotlidec-static.lib" "%__PROJECT__%\build\brotli\lib\brotlidec.lib"
copy /Y "%__PROJECT__%\build\brotli\lib\brotlicommon-static.lib" "%__PROJECT__%\build\brotli\lib\brotlicommon.lib"

type nul > "%__PROJECT__%\build\brotli\.completed"

cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "brotli build completed ! "

endlocal

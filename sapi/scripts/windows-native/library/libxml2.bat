@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  "%__PROJECT__%\build"

if exist "%__PROJECT__%\build\lib\libxml2s.lib" goto :skip

cd /d %__PROJECT__%\pool\lib\
7z.exe x -aoa -y   %__PROJECT__%\pool\lib\libxml2-v2.9.14.tar.gz

if  exist "%__PROJECT__%\thirdparty\libxml2" rmdir /s /q "%__PROJECT__%\thirdparty\libxml2"
mkdir "%__PROJECT__%\thirdparty\libxml2"

7z.exe x -aoa -y  -o"%__PROJECT__%\thirdparty\libxml2"  %__PROJECT__%\pool\lib\libxml2-v2.9.14.tar

cd /d %__PROJECT__%\thirdparty\libxml2\libxml2-2.9.14\
dir
echo %cd%

:: exit /b 0

:: libxml2:x64-windows-static

set CL=/MP
mkdir -p build_dir
cd build_dir

:: set "PKG_CONFIG_PATH=C:/vcpkg/packages/libiconv_x64-windows-static/lib/pkgconfig"

cmake ^
cmake -S .. -B . ^
-DBUILD_SHARED_LIBS=OFF ^
-DCMAKE_BUILD_TYPE=Release ^
-DCMAKE_MSVC_RUNTIME_LIBRARY=MultiThreaded ^
-DCMAKE_INSTALL_PREFIX=%__PROJECT__%\build ^
-DIconv_LIBRARY="%__PROJECT__%\build\libiconv\lib\libiconv_a.lib" ^
-DIconv_INCLUDE_DIR="%__PROJECT__%\build\libiconv\include" ^
-DLIBXML_STATIC=ON ^
-DLIBXML2_WITH_ZLIB=OFF ^
-DLIBXML2_WITH_LZMA=OFF ^
-DLIBXML2_WITH_ICU=OFF ^
-DLIBXML2_WITH_PYTHON=OFF ^
-DLIBXML2_WITH_TESTS=OFF ^
-DCMAKE_C_FLAGS="/MT /O2 /W3 /DNDEBUG" ^
-DCMAKE_C_FLAGS_RELEASE="/MT /O2 /W3 /DNDEBUG" ^
-DCMAKE_VERBOSE_MAKEFILE=ON

cmake --build . --config Release --target install

copy %__PROJECT__%\build\lib\libxml2s.lib %__PROJECT__%\build\lib\libxml2_a.lib

dumpbin /DIRECTIVES %__PROJECT__%\build\lib\libxml2_a.lib | findstr /i "DEFAULTLIB"


cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "libxml2 build completed ! "

endlocal

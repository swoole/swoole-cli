@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  %__PROJECT__%\build\freetype


if exist "%__PROJECT__%\build\freetype\.completed" goto :skip


mkdir %__PROJECT__%\var\native-build\
cd /d %__PROJECT__%\var\native-build\


set CMAKE_BUILD_PARALLEL_LEVEL=%NUMBER_OF_PROCESSORS%


cd /d %__PROJECT__%\pool\lib\
7z.exe x -aoa -y   %__PROJECT__%\pool\lib\freetype-2.13.2.tar.gz

if  exist "%__PROJECT__%\thirdparty\freetype" rmdir /s /q "%__PROJECT__%\thirdparty\freetype"
mkdir "%__PROJECT__%\thirdparty\freetype"

7z.exe x -aoa -y  -o"%__PROJECT__%\thirdparty\freetype"  %__PROJECT__%\pool\lib\freetype-2.13.2.tar
cd /d %__PROJECT__%\thirdparty\freetype\freetype-VER-2-13-2

dir



if exist "build_static" (
    rmdir /S /Q build_static
)

mkdir  build_static
cmake -B build_static -S . ^
	-DCMAKE_INSTALL_PREFIX="%__PROJECT__%\build\freetype" ^
	-DCMAKE_BUILD_TYPE=Release  ^
	-DBUILD_SHARED_LIBS=OFF  ^
	-DBUILD_STATIC_LIBS=ON  ^
	-DCMAKE_MSVC_RUNTIME_LIBRARY=MultiThreaded ^
	-DFT_REQUIRE_ZLIB=TRUE  ^
	-DFT_REQUIRE_BZIP2=TRUE  ^
	-DFT_REQUIRE_BROTLI=TRUE  ^
	-DFT_REQUIRE_PNG=TRUE  ^
	-DFT_DISABLE_HARFBUZZ=TRUE  ^
	-DCMAKE_PREFIX_PATH="%__PROJECT__%\build\zlib;%__PROJECT__%\build\bzip2;%__PROJECT__%\build\libpng;%__PROJECT__%\build\brotli" ^
	-DBZIP2_LIBRARIES="%__PROJECT__%\build\bzip2\lib\libbz2_a.lib" ^
	-DCMAKE_C_FLAGS="/MT /O2 /W3 /DNDEBUG" ^
	-DCMAKE_C_FLAGS_RELEASE="/MT /O2 /W3 /DNDEBUG" ^
	-DCMAKE_VERBOSE_MAKEFILE=ON



cmake --build build_static --config Release --target install

copy /Y "%__PROJECT__%\build\freetype\lib\freetype.lib" "%__PROJECT__%\build\freetype\lib\freetype_a.lib"

rem  Compatible with PHP build system detection
xcopy "%__PROJECT__%\build\freetype\include" "%__PROJECT__%\build\include" /E /I /H /Y

type nul > "%__PROJECT__%\build\freetype\.completed"

cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "freetype build completed ! "

exit /b 0

cd /d %__PROJECT__%

endlocal

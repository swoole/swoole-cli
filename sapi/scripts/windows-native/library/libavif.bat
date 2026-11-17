@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  %__PROJECT__%\build\libavif


if exist "%__PROJECT__%\build\libavif\.completed" goto :skip


mkdir %__PROJECT__%\var\native-build
cd /d %__PROJECT__%\var\native-build


set CMAKE_BUILD_PARALLEL_LEVEL=%NUMBER_OF_PROCESSORS%


cd /d %__PROJECT__%\pool\lib\
7z.exe x -aoa -y   %__PROJECT__%\pool\lib\libavif-v1.3.0.tar.gz

if  exist "%__PROJECT__%\thirdparty\libavif" rmdir /s /q "%__PROJECT__%\thirdparty\libavif"
mkdir "%__PROJECT__%\thirdparty\libavif"

7z.exe x -aoa -y  -o"%__PROJECT__%\thirdparty\libavif"  %__PROJECT__%\pool\lib\libavif-v1.3.0.tar
cd %__PROJECT__%\thirdparty\libavif\libavif-1.3.0

dir

set "PATH=%__PROJECT__%\runtime\nasm;%PATH%;"

if exist "build_static" (
    rmdir /S /Q build_static
)

rem docs: https://github.com/AOMediaCodec/libavif/

mkdir  build_static
cmake -B build_static -S . ^
	-DCMAKE_INSTALL_PREFIX="%__PROJECT__%\build\libavif" ^
	-DCMAKE_BUILD_TYPE=Release  ^
	-DBUILD_SHARED_LIBS=OFF  ^
	-DBUILD_STATIC_LIBS=ON  ^
	-DCMAKE_MSVC_RUNTIME_LIBRARY=MultiThreaded ^
	-DCMAKE_C_FLAGS="/MT /O2 /W3 /DNDEBUG" ^
	-DCMAKE_C_FLAGS_RELEASE="/MT /O2 /W3 /DNDEBUG" ^
	-DCMAKE_PREFIX_PATH="%__PROJECT__%\build\dav1d;%__PROJECT__%\build\svt_av1;%__PROJECT__%\build\libwebp;%__PROJECT__%\build\libyuv;%__PROJECT__%\build\libwebp;%__PROJECT__%\build\libpng;%__PROJECT__%\build\libjpeg" ^
	-DCMAKE_VERBOSE_MAKEFILE=ON ^
	-DAVIF_CODEC_AOM=OFF ^
	-DAVIF_CODEC_DAV1D=SYSTEM ^
	-DAVIF_CODEC_LIBGAV1=OFF ^
	-DAVIF_CODEC_RAV1E=OFF ^
	-DAVIF_CODEC_SVT=SYSTEM ^
	-DAVIF_LIBYUV=ON ^
	-DAVIF_LIBSHARPYUV=ON ^
	-DAVIF_JPEG=ON ^
	-DAVIF_ZLIBPNG=ON ^
	-DAVIF_BUILD_APPS=OFF ^
	-DAVIF_BUILD_TESTS=OFF ^
	-DAVIF_BUILD_EXAMPLES=OFF ^
	-DDAV1D_LIBRARIES="%__PROJECT__%\build\dav1d\lib\libbz2_a.lib" ^



cmake --build build_static --config Release --target install

copy /Y "%__PROJECT__%\build\libavif\lib\avif.lib" "%__PROJECT__%\build\libavif\lib\avif_a.lib"
rem  Compatible with PHP build detection
xcopy "%__PROJECT__%\build\libavif\include" "%__PROJECT__%\build\include" /E /I /H /Y


if exist "%__PROJECT__%\build\libavif\lib\avif.lib" (
    type nul > "%__PROJECT__%\build\libavif\.completed"
)

cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "libavif build completed ! "

exit /b 0

cd /d %__PROJECT__%

endlocal

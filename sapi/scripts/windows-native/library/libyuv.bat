@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  %__PROJECT__%\build\libyuv


if exist "%__PROJECT__%\build\libyuv\.completed" goto :skip


mkdir %__PROJECT__%\var\native-build
cd /d %__PROJECT__%\var\native-build


set CMAKE_BUILD_PARALLEL_LEVEL=%NUMBER_OF_PROCESSORS%


cd /d %__PROJECT__%\pool\lib\
7z.exe x -aoa -y   %__PROJECT__%\pool\lib\libyuv-b0f72309c6c0b952d0198be5a5b5106f089fe1c5.tar.gz

if  exist "%__PROJECT__%\thirdparty\libyuv" rmdir /s /q "%__PROJECT__%\thirdparty\libyuv"
mkdir "%__PROJECT__%\thirdparty\libyuv"

7z.exe x -aoa -y  -o"%__PROJECT__%\thirdparty\libyuv"  %__PROJECT__%\pool\lib\libyuv-b0f72309c6c0b952d0198be5a5b5106f089fe1c5.tar
cd %__PROJECT__%\thirdparty\libyuv

dir

set "PATH=%__PROJECT__%\runtime\nasm;%PATH%;"

if exist "build_static" (
    rmdir /S /Q build_static
)

rem docs: https://chromium.googlesource.com/libyuv/libyuv

sed.exe -i.backup "s/^\(add_library( \${ly_lib_shared} SHARED \${ly_lib_parts})\)/# \1/" CMakeLists.txt
sed.exe -i.backup "s/^\(  target_link_libraries( \${ly_lib_shared} \${JPEG_LIBRARY} )\)/# \1/" CMakeLists.txt
sed.exe -i.backup "s/^\(set_target_properties( \${ly_lib_shared} PROPERTIES\)/# \1/" CMakeLists.txt
sed.exe -i.backup "s/^\(  set_target_properties( \${ly_lib_shared} PROPERTIES\)/# \1/" CMakeLists.txt
sed.exe -i.backup "s/^\(install ( TARGETS \${ly_lib_shared} LIBRARY\)/# \1/" CMakeLists.txt

mkdir  build_static
cmake -B build_static -S . ^
	-DCMAKE_INSTALL_PREFIX="%__PROJECT__%\build\libyuv" ^
	-DCMAKE_BUILD_TYPE=Release  ^
	-DBUILD_SHARED_LIBS=OFF  ^
	-DBUILD_STATIC_LIBS=ON  ^
	-DCMAKE_MSVC_RUNTIME_LIBRARY=MultiThreaded ^
	-DCMAKE_C_FLAGS="/MT /O2 /W3 /DNDEBUG" ^
	-DCMAKE_C_FLAGS_RELEASE="/MT /O2 /W3 /DNDEBUG" ^
	-DCMAKE_CXX_FLAGS="/MT /O2 /W3 /DNDEBUG" ^
	-DCMAKE_CXX_FLAGS_RELEASE="/MT /O2 /W3 /DNDEBUG" ^
    -DCMAKE_PREFIX_PATH="%__PROJECT__%\build\libjpeg;" ^
	-DCMAKE_VERBOSE_MAKEFILE=ON

cmake --build build_static --config Release --target install

type nul > "%__PROJECT__%\build\libyuv\.completed"

cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "libyuv build completed ! "

exit /b 0

cd /d %__PROJECT__%

endlocal

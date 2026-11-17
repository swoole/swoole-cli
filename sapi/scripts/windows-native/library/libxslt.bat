@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  %__PROJECT__%\build\libxslt

if exist "%__PROJECT__%\build\libxslt\.completed" goto :skip


mkdir %__PROJECT__%\var\native-build\
cd /d %__PROJECT__%\var\native-build\


:: https://github.com/winlibs/libxslt.git
if not exist "libxslt" (
   git clone -b libxslt-1.1.43 https://github.com/winlibs/libxslt.git
)

if  exist "%__PROJECT__%\thirdparty\libxslt" rmdir /s /q "%__PROJECT__%\thirdparty\libxslt"
mkdir "%__PROJECT__%\thirdparty\libxslt"

xcopy libxslt %__PROJECT__%\thirdparty\libxslt /E /I /H /Y

cd "%__PROJECT__%\thirdparty\libxslt"


if exist "build_static" (
    rmdir /S /Q build_static
)

mkdir  build_static
cmake -B build_static -S . ^
  -DCMAKE_INSTALL_PREFIX="%__PROJECT__%\build\libxslt" ^
  -DCMAKE_BUILD_TYPE=Release  ^
  -DBUILD_SHARED_LIBS=OFF  ^
  -DBUILD_STATIC_LIBS=ON  ^
  -DCMAKE_MSVC_RUNTIME_LIBRARY=MultiThreaded ^
  -DLIBXSLT_WITH_PROFILER=OFF ^
  -DLIBXSLT_WITH_PROGRAMS=OFF ^
  -DLIBXSLT_WITH_PYTHON=OFF ^
  -DLIBXSLT_WITH_TESTS=OFF ^
  -DCMAKE_PREFIX_PATH="%__PROJECT__%\build"^
  -DIconv_LIBRARY="%__PROJECT__%\build\libiconv\lib\libiconv_a.lib" ^
  -DIconv_INCLUDE_DIR="%__PROJECT__%\build\libiconv\include" ^
  -DCMAKE_VERBOSE_MAKEFILE=ON



cmake --build build_static --config Release --target install

copy /Y "%__PROJECT__%\build\libxslt\lib\libexslts.lib" "%__PROJECT__%\build\libxslt\lib\libexslt_a.lib"
copy /Y "%__PROJECT__%\build\libxslt\lib\libxslts.lib" "%__PROJECT__%\build\libxslt\lib\libxslt_a.lib"

type nul > "%__PROJECT__%\build\libxslt\.completed"

cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "libxslt build completed ! "

exit /b 0

cd /d %__PROJECT__%

endlocal

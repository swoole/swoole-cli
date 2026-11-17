@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  %__PROJECT__%\build\libedit

exit /b 0

if exist "%__PROJECT__%\build\libedit\.completed" goto :skip


mkdir %__PROJECT__%\var\native-build\
cd /d %__PROJECT__%\var\native-build\


:: https://github.com/larkmjc/libedit.git
if not exist "libedit" (
:: php.exe -c %__PROJECT__%\runtime\php\php.ini  pie.phar download phpredis/phpredis:^6.3
   git clone https://github.com/larkmjc/libedit.git
)

if  exist "%__PROJECT__%\thirdparty\libedit" rmdir /s /q "%__PROJECT__%\thirdparty\libedit"
mkdir "%__PROJECT__%\thirdparty\libedit"

xcopy libedit %__PROJECT__%\thirdparty\libedit /E /I /H /Y

cd "%__PROJECT__%\thirdparty\libedit"

sed.exe -i.bak "s|Include/\*|Include/10.*|" CMakeLists.txt


if exist "build_static" (
    rmdir /S /Q build_static
)

mkdir  build_static
cmake -B build_static -S . ^
  -DCMAKE_INSTALL_PREFIX="%__PROJECT__%\build\libedit" ^
  -DCMAKE_BUILD_TYPE=Release  ^
  -DBUILD_SHARED_LIBS=OFF  ^
  -DBUILD_STATIC_LIBS=ON  ^
  -DCMAKE_MSVC_RUNTIME_LIBRARY=MultiThreaded ^
  -DCMAKE_VERBOSE_MAKEFILE=ON

cmake --build build_static --config Release --target install


type nul > "%__PROJECT__%\build\libedit\.completed"

cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "libedit build completed ! "

exit /b 0

cd /d %__PROJECT__%

endlocal

@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\


set __PROJECT__=%cd%
cd /d %__PROJECT__%
mkdir  %__PROJECT__%\build\icu\
mkdir  %__PROJECT__%\build\icu\lib
mkdir  %__PROJECT__%\build\icu\include

if exist "%__PROJECT__%\build\icu\.completed" goto :skip

:: method 1
rem  Cygwin + MSVC
rem  baash runConfigureICU Cygwin/MSVC --enable-static

:: method 2
rem MSBuild Fork k0michi/icu
rem msbuild source\allinone\allinone.sln /p:Configuration=Release /p:Platform=x64 /p:SkipUWP=true

:: method 3
rem vcpkg
rem vcpkg install icu:x64-windows-static


set "VCPKG_ROOT=%__PROJECT__%\var\native-build\vcpkg"
set PATH=%VCPKG_ROOT%;%PATH%
vcpkg install icu:x64-windows-static

xcopy "%__PROJECT__%\var\native-build\vcpkg\packages\icu_x64-windows-static\include" "%__PROJECT__%\build\icu\include" /E /I /H /Y
xcopy "%__PROJECT__%\var\native-build\vcpkg\packages\icu_x64-windows-static\lib" "%__PROJECT__%\build\icu\lib" /E /I /H /Y

type nul > "%__PROJECT__%\build\icu\.completed"

cd /d %__PROJECT__%
goto :ok

:skip
echo "skip build step"
goto :ok

:ok
echo "icu build completed ! "

exit /b 0













:: set "ORIGIN_PATH=%PATH%"
:: set "PATH=%__PROJECT__%\runtime\nasm\;"
:: set "PATH=C:\cygwin64\bin\;%PATH%;"
:: set "PATH=%__PROJECT__%\var\native-build\php-sdk-binary-tools\bin\;%__PROJECT__%\var\native-build\php-sdk-binary-tools\msys2\bin;%PATH%;"
:: set "PATH=%PATH%;%ORIGIN_PATH%"
:: echo "%PATH%"



:: where link.exe



:: exit /b 0

:: set "CXXFLAGS=/MT"
:: set "CFLAGS=/EHsc /MP /MT /UCRT"
:: set "CFLAGS=/EHsc /MP /MT"
:: set "CXXFLAGS=/MT"

:: set "CPPFLAGS=-D U_CHARSET_IS_UTF8=1  -D U_USING_ICU_NAMESPACE=1  -D U_STATIC_IMPLEMENTATION=1 "

sed.exe -i.bak "s/RELEASE_CFLAGS='-Gy -MD'/RELEASE_CFLAGS='-Gy -MT'/" .\source\runConfigureICU
sed.exe -i.bak "s/RELEASE_CXXFLAGS='-Gy -MD'/RELEASE_CXXFLAGS='-Gy -MT'/" .\source\runConfigureICU

bash ./source/runConfigureICU Cygwin/MSVC --prefix=/cygdrive/d/a/swoole-cli/swoole-cli/build/icu ^
--enable-static=yes ^
--enable-shared=no ^
--with-data-packaging=static ^
--enable-release=yes ^
--enable-extras=yes ^
--enable-icuio=yes ^
--enable-dyload=no ^
--enable-tools=no ^
--enable-tests=no ^
--enable-samples=no

make -j %NUMBER_OF_PROCESSORS%
make install

if exist %__PROJECT__%\backup\git-link.exe (
   move  %__PROJECT__%\backup\git-link.exe "C:\Program Files\Git\usr\bin\link.exe"
)

if exist %__PROJECT__%\backup\cygwin-link.exe (
   move %__PROJECT__%\backup\cygwin-link.exe "C:\cygwin64\bin\link.exe"
)

cd /d %__PROJECT__%
endlocal


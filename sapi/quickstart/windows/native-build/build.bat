@echo off

rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%

rem cmd /k "C:\Program Files (x86)\Microsoft Visual Studio\2019\Community\VC\Auxiliary\Build\vcvarsall.bat" amd64
rem cmd /k  sapi\quickstart\windows\native-build\native-build-php-sdk-vs2019.bat

cmd /k  sapi\quickstart\windows\native-build\native-build-php-config.bat
cmd /k  sapi\quickstart\windows\native-build\native-build-php-build.bat
cmd /k  sapi\quickstart\windows\native-build\native-build-php-release.bat


cd /d %__PROJECT__%

set __PROJECT__=

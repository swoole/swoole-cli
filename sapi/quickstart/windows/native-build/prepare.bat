@echo off

rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%


start /wait sapi\quickstart\windows\native-build\install-vc-runtime.bat

start /wait sapi\quickstart\windows\native-build\install-visualstudio-2019.bat

start /wait  sapi\quickstart\windows\native-build\install-deps-soft.bat

cd /d %__PROJECT__%

set __PROJECT__=

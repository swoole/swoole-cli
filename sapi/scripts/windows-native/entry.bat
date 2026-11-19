@echo off

setlocal enabledelayedexpansion
rem show current file location
echo %~dp0
cd /d %~dp0
cd /d ..\..\..\

set "__PROJECT__=%cd%"
echo %cd%

::var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\library\zlib.bat
var\native-build\php-sdk-binary-tools\phpsdk-starter.bat -c vs17 -a x64  -t sapi\scripts\windows-native\main.bat

cd /d %__PROJECT__%
endlocal

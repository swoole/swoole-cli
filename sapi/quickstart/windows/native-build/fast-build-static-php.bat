@echo off

setlocal
rem show current file location
echo %~dp0
cd /d %~dp0
cd /d ..\..\..\..\

set "__PROJECT__=%cd%"
echo %cd%

rem start cmd /k  %__PROJECT__%
rem start /d %__PROJECT__%

rem open new cmd termnial
start cmd /k sapi\quickstart\windows\native-build\install-vc-runtime.bat
start cmd /k sapi\quickstart\windows\native-build\install-deps-soft.bat
start cmd /k sapi\quickstart\windows\native-build\install-visualstudio.bat

exit 0
sapi\quickstart\windows\native-build\native-build-php-sdk-vs2019.bat
for /f "delims=" %%i in ('set') do  start cmd /k sapi\quickstart\windows\native-build\native-build-php-config.bat
for /f "delims=" %%i in ('set') do  start cmd /k sapi\quickstart\windows\native-build\native-build-php-config-help.bat
start cmd /k sapi\quickstart\windows\native-build\native-build-php-build.bat
start cmd /k sapi\quickstart\windows\native-build\native-build-php-build-release.bat
start cmd /k sapi\quickstart\windows\native-build\native-build-php-archive.bat

endlocal

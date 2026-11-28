@echo off

setlocal enabledelayedexpansion

rem show current file location
echo %~dp0
cd /d %~dp0
cd /d ..\..\..\

set "PATH=%__PROJECT__%\var\native-build\php-sdk-binary-tools\bin\;%__PROJECT__%\var\native-build\php-sdk-binary-tools\msys2\bin;%PATH%;"

set "__PROJECT__=%cd%"
echo %cd%
cd %__PROJECT__%

dumpbin /DEPENDENTS ".\x64\Release_TS\php.exe"

set "SWOOLE_VERSION="
for /f "tokens=2" %%V in ('.\x64\Release_TS\php.exe -v 2^>nul ^| findstr /b "Swoole "') do (
    if not defined SWOOLE_VERSION set "SWOOLE_VERSION=%%V"
)

echo SWOOLE_VERSION=!SWOOLE_VERSION!

mkdir bin
copy /Y ".\x64\Release_TS\php.exe" bin\swoole-cli.exe

cd "%__PROJECT__%\bin"

7z a -tzip %__PROJECT__%\swoole-cli-v%SWOOLE_VERSION%-windows-x64.zip swoole-cli.exe

cd "%__PROJECT__%"

dir

exit /b 0

cd /d %__PROJECT__%
endlocal

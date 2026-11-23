@echo off

setlocal enabledelayedexpansion
rem show current file location
echo %~dp0
cd /d %~dp0
cd /d ..\..\..\

set "__PROJECT__=%cd%"
cd %__PROJECT__%

cmd /c sapi\scripts\windows-native\extension\swoole.bat
cmd /c sapi\scripts\windows-native\extension\redis.bat


cd /d %__PROJECT__%
endlocal

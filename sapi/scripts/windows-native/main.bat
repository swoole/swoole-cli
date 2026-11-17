@echo off

setlocal enabledelayedexpansion
rem show current file location
echo %~dp0
cd /d %~dp0
cd /d ..\..\..\

set "__PROJECT__=%cd%"
echo %cd%

call %__PROJECT__%\sapi\scripts\windows-native\clean.bat
call %__PROJECT__%\sapi\scripts\windows-native\build-libs.bat
call %__PROJECT__%\sapi\scripts\windows-native\build-extensions.bat

call %__PROJECT__%\sapi\scripts\windows-native\config.bat
call %__PROJECT__%\sapi\scripts\windows-native\x_custom_config.bat
call %__PROJECT__%\sapi\scripts\windows-native\build.bat


cd /d %__PROJECT__%
endlocal

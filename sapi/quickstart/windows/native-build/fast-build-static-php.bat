@echo off

setlocal
rem show current file location
echo %~dp0
cd /d %~dp0
cd /d ..\..\..\..\

set "__PROJECT__=%cd%"
echo %cd%

start /d %__PROJECT__%

endlocal

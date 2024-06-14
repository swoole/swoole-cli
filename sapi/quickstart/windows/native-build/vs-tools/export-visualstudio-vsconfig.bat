@echo off

setlocal
rem show current file location
echo %~dp0
cd /d %~dp0
cd /d .\..\..\..\..\..\

set "__PROJECT__=%cd%"
echo %cd%

VisualStudioSetup.exe export 	--passive  --force

endlocal

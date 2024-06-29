@echo off

setlocal
rem show current file location
echo %~dp0
cd /d %~dp0
cd /d .\..\..\..\..\

set "__PROJECT__=%cd%"
echo %cd%


vc_redist.x64.exe /install /passive /norestart
vc_redist.x86.exe /install /passive /norestart


endlocal

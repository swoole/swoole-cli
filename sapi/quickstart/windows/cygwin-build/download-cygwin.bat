@echo off

setlocal

echo %~dp0
cd /d %~dp0
cd /d ..\..\..\..\

set "__PROJECT__=%cd%"
echo %cd%

md %__PROJECT__%\var\windows-cygwin-build-deps\

cd /d %__PROJECT__%\var\windows-cygwin-build-deps\

:: cygwin site: https://cygwin.com/
curl.exe -fSLo setup-x86_64.exe https://cygwin.com/setup-x86_64.exe


:: exit /b 0

cd /d %__PROJECT__%\

endlocal

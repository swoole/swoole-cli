@echo off


setlocal enableextensions enabledelayedexpansion

echo %~dp0
cd /d %~dp0
cd /d ..\..\..\..\

set "__PROJECT__=%cd%"
cd /d %__PROJECT__%\
echo %cd%


SET DIRECTORY_NAME="c:\cygwin64"
C:\windows\system32\TAKEOWN /f %DIRECTORY_NAME% /r /d y
C:\windows\system32\ICACLS %DIRECTORY_NAME% /grant administrators:F /t

rmdir /s %DIRECTORY_NAME%

endlocal

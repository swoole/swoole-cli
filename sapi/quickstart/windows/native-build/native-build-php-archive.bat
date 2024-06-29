@echo off

setlocal
rem show current file location
echo %~dp0
cd /d %~dp0
cd /d ..\..\..\..\

set "__PROJECT__=%cd%"
echo %cd%
cd /d %__PROJECT__%\
dir

rem cd %__PROJECT__%\php-src\x64\Release_TS\
cd /d %__PROJECT__%\php-src\x64\Release\
dir

rem .\php -v
rem .\php -m

endlocal

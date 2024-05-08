@echo off
echo %~dp0
SET  CURRENT_DIR=%~dp0
cd %~dp0

cd ..\..\..\..\
echo %cd%

SET PROJECT_DIR=%cd%

cd %PROJECT_DIR%\php-src
echo %cd%

nmake

dir %PROJECT_DIR%\php-src\x64\Release_TS\
%PROJECT_DIR%\php-src\x64\Release_TS\php.exe -v
%PROJECT_DIR%\php-src\x64\Release_TS\php.exe -m
cd %PROJECT_DIR%\

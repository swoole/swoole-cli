@echo off
echo %~dp0
cd %~dp0

cd ..\..\..\..\
echo %cd%

SET PROJECT_DIR=%cd%

cd %PROJECT_DIR%\
echo %cd%

%PROJECT_DIR%\php-sdk-binary-tools\phpsdk-vs17-x64.bat

cd %PROJECT_DIR%\

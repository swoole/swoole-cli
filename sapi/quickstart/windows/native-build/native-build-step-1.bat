@echo off
echo %~dp0
SET  CURRENT_DIR=%~dp0
cd %~dp0

cd ..\..\..\..\
echo %cd%

SET PROJECT_DIR=%cd%

cd %PROJECT_DIR%\php-sdk-binary-tools
echo %cd%

phpsdk-vs17-x64.bat

cd %PROJECT_DIR%\

@echo off
echo %~dp0
SET  CURRENT_DIR=%~dp0
cd %~dp0

cd ..\..\..\..\
echo %cd%

SET PROJECT_DIR=%cd%

cd %PROJECT_DIR%\php-src
echo %cd%

buildconf
configure --help
configure --disable-all --enable-cli --enable-static=yes --enable-shared=no
nmake

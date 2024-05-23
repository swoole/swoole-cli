@echo off
echo %~dp0
cd %~dp0

cd ..\..\..\..\
echo %cd%

SET PROJECT_DIR=%cd%

cd %PROJECT_DIR%\
echo %cd%

rem %PROJECT_DIR%\php-sdk-binary-tools\phpsdk-vs17-x64.bat


set PHP_SDK_ARCH=x64
set PHP_SDK_BIN_PATH=%PROJECT_DIR%\php-sdk-binary-tools\bin\
set PHP_SDK_MSYS2_PATH=%PROJECT_DIR%\php-sdk-binary-tools\msys2\usr\bin\
set PHP_SDK_OS_ARCH=x64
set PHP_SDK_PHP_CMD=%PROJECT_DIR%\php-sdk-binary-tools\bin\php\do_php.bat
set PHP_SDK_ROOT_PATH=%PROJECT_DIR%\php-sdk-binary-tools\
set "PHP_SDK_VC_DIR=C:\Program Files\Microsoft Visual Studio\2022\Community\VC"
set "PHP_SDK_VC_TOOLSET_VER=%VCToolsVersion%"
set PHP_SDK_VS=vs17
set PHP_SDK_VS_NUM=17
set "PHP_SDK_VS_SHELL_CMD=C:\Program Files\Microsoft Visual Studio\2022\Community\VC\Auxiliary\Build\vcvarsall.bat amd64"
set "PATH=%PROJECT_DIR%\php-sdk-binary-tools\bin;%PROJECT_DIR%\php-sdk-binary-tools\msys2\usr\bin;%PATH%"
echo %PATH%

cd %PROJECT_DIR%\

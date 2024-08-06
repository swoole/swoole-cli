@echo off

rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%

rem %__PROJECT__%\php-sdk-binary-tools\phpsdk-vs16-x64.bat


set PHP_SDK_ARCH=x64
set PHP_SDK_BIN_PATH=%__PROJECT__%\php-sdk-binary-tools\bin\
set PHP_SDK_MSYS2_PATH=%__PROJECT__%\php-sdk-binary-tools\msys2\usr\bin\
set PHP_SDK_OS_ARCH=x64
set PHP_SDK_PHP_CMD=%__PROJECT__%\php-sdk-binary-tools\bin\php\do_php.bat
set PHP_SDK_ROOT_PATH=%__PROJECT__%\php-sdk-binary-tools\
set "PHP_SDK_VC_DIR=C:\Program Files (x86)\Microsoft Visual Studio\2019\Community\VC"
set "PHP_SDK_VC_TOOLSET_VER=%VCToolsVersion%"
set PHP_SDK_VS=vs16
set PHP_SDK_VS_NUM=16
set "PHP_SDK_VS_SHELL_CMD=C:\Program Files (x86)\Microsoft Visual Studio\2019\Community\VC\Auxiliary\Build\vcvarsall.bat amd64"
set "PATH=%__PROJECT__%\php-sdk-binary-tools\bin;%__PROJECT__%\php-sdk-binary-tools\msys2\usr\bin;%__PROJECT__%\php\;%__PROJECT__%\nasm\;C:\Strawberry\perl\bin;C:\Program Files\Git\bin;%PATH%"
echo %PATH%

cd /d %__PROJECT__%

set __PROJECT__=

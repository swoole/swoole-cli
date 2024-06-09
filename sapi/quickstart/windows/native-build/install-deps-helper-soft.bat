@echo off

rem chcp 65001

setlocal
rem show current file location
echo %~dp0
cd /d %~dp0
cd /d .\..\..\..\..\

set "__PROJECT__=%cd%"
echo %cd%


rem silent installation msi

rem start /wait "" "安装程序路径.exe" /SILENT /NORESTART


:: 设置Git安装路径
set "INSTALL_PATH=C:\Program Files\Git"

:: 创建安装目录
if not exist "%INSTALL_PATH%" mkdir "%INSTALL_PATH%"

:: 静默安装Git
:: 查看git 安装参数
:: %__PROJECT__%\Git-2.45.1-64-bit.exe /?
start /wait "" "%__PROJECT__%\Git-2.45.1-64-bit.exe" /VERYSILENT /NORESTART /NOCANCEL /SP- /CLOSEONEXIT=1 /DIR="%INSTALL_PATH%"



:: 更新环境变量

echo git installing
set "PATH=%PATH%;%__PROJECT__%\php\;%__PROJECT__%\nasm\;C:\Strawberry\perl\bin;C:\Program Files\Git\bin;%__PROJECT__%\curl-8.8.0_1-win64-mingw\bin;%__PROJECT__%\libarchive\bin;"
echo %PATH%


perl -v
php -v
nasm -v
git version
curl -V

endlocal

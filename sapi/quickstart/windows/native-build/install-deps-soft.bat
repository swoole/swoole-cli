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
rem msiexec /i strawberry-perl-5.38.2.2-64bit.msi /quiet

msiexec /i strawberry-perl-5.38.2.2-64bit.msi  /passive


rem start /wait "" "安装程序路径.exe" /SILENT /NORESTART


:: 设置Git安装路径
set "INSTALL_PATH=C:\Program Files\Git"

:: 创建安装目录
if not exist "%INSTALL_PATH%" mkdir "%INSTALL_PATH%"

:: 静默安装Git
%__PROJECT__%\Git-2.45.1-64-bit.exe /?
start /wait "" "%__PROJECT__%\Git-2.45.1-64-bit.exe" /VERYSILENT /NORESTART /NOCANCEL /SP- /CLOSEONEXIT=1 /DIR="%INSTALL_PATH%"



:: 更新环境变量
call refreshenv

set "PATH=%PATH%;%__PROJECT__%\php\;%__PROJECT__%\nasm\;C:\Strawberry\perl\bin"
echo %PATH%


perl -v
php -v
nasm -v
git version

endlocal

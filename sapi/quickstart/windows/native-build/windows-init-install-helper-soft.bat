setlocal


echo %~dp0
cd /d %~dp0
cd /d .\..\..\..\..\

set "__PROJECT__=%cd%"
echo %cd%

md %__PROJECT__%\var\windows-build-deps\

cd /d %__PROJECT__%\var\windows-build-deps\

rem start /wait .\Git-2.47.1-64-bit.exe /VERYSILENT /NORESTART /NOCANCEL /SP- /CLOSEONEXIT=1 /DIR="C:\Program Files\Git"

:: 设置Git安装路径
set "INSTALL_PATH=C:\Program Files\Git"

:: 创建安装目录
if not exist "%INSTALL_PATH%" mkdir "%INSTALL_PATH%"

:: 静默安装Git
:: 查看git 安装参数
:: .\Git-2.47.1-64-bit.exe /?
:: start /wait "" "%__PROJECT__%\var\windows-build-deps\Git-2.47.1-64-bit.exe" /VERYSILENT /NORESTART /NOCANCEL /SP- /CLOSEONEXIT=1 /DIR="%INSTALL_PATH%"





endlocal

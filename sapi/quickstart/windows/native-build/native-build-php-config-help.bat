@echo off

setlocal
rem 显示当前脚本所在目录
echo %~dp0
cd /d %~dp0
cd /d ..\..\..\..\

set "__PROJECT__=%cd%"
echo %cd%
cd %__PROJECT__%\php-src

configure.bat --help

cd %__PROJECT__%

endlocal

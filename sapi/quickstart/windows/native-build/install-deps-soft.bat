@echo off

cd %~dp0
echo %~dp0

cd ..\..\..\..\

set __PROJECT__=%cd%
echo %cd%


:: 命令行静默安装 msi
::  msiexec /i strawberry-perl-5.38.2.2-64bit.msi /quiet

msiexec /i strawberry-perl-5.38.2.2-64bit.msi /passive

vc_redist.x64.exe /install /passive /norestart
vc_redist.x86.exe /install /passive /norestart

set PATH="%PATH%;%__PROJECT__%\php\;%__PROJECT__%\nasm\"
echo %PATH%

php -v
nasm -v

@echo off

cd %~dp0
echo %~dp0

cd ..\..\..\..\

echo %cd%


: 命令行静默安装 msi
:  msiexec /i strawberry-perl-5.38.2.2-64bit.msi /quiet

msiexec /i strawberry-perl-5.38.2.2-64bit.msi /passive


set PATH="%PATH%;%cd%\php\;%cd%\nasm\"
echo %PATH%

php -v
nasm -v

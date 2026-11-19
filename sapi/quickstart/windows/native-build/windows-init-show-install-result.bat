@echo off

setlocal


echo %~dp0


cd /d %~dp0
cd /d ..\..\..\..\


set "__PROJECT__=%cd%"
echo %cd%

md %__PROJECT__%\var\native-build\
md %__PROJECT__%\runtime\

cd /d %__PROJECT__%\var\native-build\


echo %ProgramFiles(x86)%
echo %USERPROFILE%
echo %NUMBER_OF_PROCESSORS%
echo %ProgramFiles%
set "PATH=%ProgramFiles%\Git\bin;%__PROJECT__%\runtime\nasm\;%__PROJECT__%\runtime\php;%__PROJECT__%\runtime\libarchive\bin;%PATH%"
set "PATH=%__PROJECT__%\var\native-build\php-sdk-binary-tools\bin\;%__PROJECT__%\var\native-build\php-sdk-binary-tools\msys2\bin;%PATH%"
echo "%PATH%"

:: git config --global core.autocrlf false
:: git config --global core.eol lf
:: git config --global core.ignorecase false

:: vswhere find  Visual Studio component
:: https://github.com/microsoft/vswhere/wiki/Examples


vswhere -?
vswhere -all

where perl
perl -v
nasm -v
where bison.exe
where  re2c.exe
git version
curl -V
:: dir %__PROJECT__%\runtime\php\ext\
:: php -c %__PROJECT__%\runtime\php.ini -v
:: php -c %__PROJECT__%\runtime\php.ini -m
:: php -c %__PROJECT__%\runtime\php.ini --ri curl
:: php -c %__PROJECT__%\runtime\php.ini composer.phar list
php -version
php -m
php --ri curl
php  composer.phar list

set

endlocal

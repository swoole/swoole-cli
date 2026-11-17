@echo off

setlocal
rem show current file location
echo %~dp0
cd %~dp0
cd ..\..\..\..\

set __PROJECT__=%cd%
cd /d %__PROJECT__%

cd /d %__PROJECT__%\var\native-build\php-src\
set "PHP_SRC=%cd%"
cd /d %__PROJECT__%\var\native-build\
echo %cd%


set "PATH=%__PROJECT__%\runtime\php;%PATH%;"

php.exe  -c %__PROJECT__%\runtime\php\php.ini --ri curl
php.exe  -c %__PROJECT__%\runtime\php\php.ini --ri openssl

:: https://packagist.org/packages/phpredis/phpredis
if not exist "redis" (
:: php.exe -c %__PROJECT__%\runtime\php\php.ini  pie.phar download phpredis/phpredis:^6.3
   git clone -b 6.3.0 https://github.com/phpredis/phpredis.git redis
)

if exist "%__PROJECT__%\var\native-build\php-src\ext\redis" (
    rd /S /Q "%__PROJECT__%\var\native-build\php-src\ext\redis"
)

xcopy "redis" "%__PROJECT__%\var\native-build\php-src\ext\redis" /E /I /Y


cd %__PROJECT__%
endlocal

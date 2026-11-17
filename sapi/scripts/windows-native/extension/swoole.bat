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

:: if not exist "swoole" git clone -b v6.3.0-rc1 --depth=1 https://github.com/swoole/swoole-src.git swoole
if not exist "swoole" (
    git clone -b master https://github.com/swoole/swoole-src.git swoole
) else (
	git -C  swoole pull
)

if exist "%__PROJECT__%\var\native-build\php-src\ext\swoole" (
    rd /S /Q "%__PROJECT__%\var\native-build\php-src\ext\swoole"
)

xcopy "swoole" "%__PROJECT__%\var\native-build\php-src\ext\swoole" /E /I /Y


cd %__PROJECT__%
endlocal

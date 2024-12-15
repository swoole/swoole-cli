@echo off

setlocal


echo %~dp0
cd /d %~dp0
cd /d ..\..\..\..\

set "__PROJECT__=%cd%"
echo %cd%



call "%__PROJECT__%\var\windows-build-deps\php-sdk-binary-tools\phpsdk-vs17-x64.bat"
call "%__PROJECT__%\sapi\quickstart\windows\native-build\native-build-php-config.bat"


if %ERRORLEVEL% neq 0 (
    echo 命令执行失败，退出状态为: %ERRORLEVEL%
    exit /b %ERRORLEVEL%
)

exit /b 0

endlocal

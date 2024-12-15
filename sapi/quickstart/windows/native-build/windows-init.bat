@echo off

setlocal


echo %~dp0
cd /d %~dp0
cd /d ..\..\..\..\

set "__PROJECT__=%cd%"
echo %cd%


if not exist "%__PROJECT__%\var\windows-build-deps\php-8.4.1-nts-Win32-vs17-x64.zip" (
    echo "windows php runtime no found "
    call "%__PROJECT__%\sapi\quickstart\windows\native-build\windows-init-download.bat"
)

rem call "%__PROJECT__%\sapi\quickstart\windows\native-build\windows-init-install.bat"
 call "%__PROJECT__%\sapi\quickstart\windows\native-build\windows-init-install-vs-tools.bat"
rem call "%__PROJECT__%\sapi\quickstart\windows\native-build\windows-init-uninstall-vs-tools.bat"

endlocal

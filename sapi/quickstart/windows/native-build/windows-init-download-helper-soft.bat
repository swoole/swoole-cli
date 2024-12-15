setlocal


echo %~dp0
cd /d %~dp0
cd /d .\..\..\..\..\

set "__PROJECT__=%cd%"
echo %cd%

md %__PROJECT__%\var\windows-build-deps\

cd /d %__PROJECT__%\var\windows-build-deps\

:: set http_proxy=http://127.0.0.1:8016
:: set https_proxy=http://127.0.0.1:8016


curl.exe -fSLo npp.8.6.7.Installer.x64.exe https://github.com/notepad-plus-plus/notepad-plus-plus/releases/download/v8.6.7/npp.8.6.7.Installer.x64.exe
curl.exe -fSLo socat-v1.8.0.1-cygwin-x64.zip https://github.com/jingjingxyk/build-static-socat/releases/download/v2.2.1/socat-v1.8.0.1-cygwin-x64.zip
curl.exe -fSLo Microsoft.WindowsTerminal_1.21.3231.0_8wekyb3d8bbwe.msixbundle https://github.com/microsoft/terminal/releases/download/v1.21.3231.0/Microsoft.WindowsTerminal_1.21.3231.0_8wekyb3d8bbwe.msixbundle
curl.exe -fSLo winget-install.ps1 https://github.com/asheroto/winget-install/releases/latest/download/winget-install.ps1
curl.exe -fSLo chocolatey-install.ps1 https://community.chocolatey.org/install.ps1
curl.exe -fSLo scoop-install.ps1 https://get.scoop.sh





:: curl.exe -fSLo npp.8.6.7.Installer.x64.exe https://php-cli.jingjingxyk.com/npp.8.6.7.Installer.x64.exe
:: curl.exe -fSLo socat-v1.8.0.1-cygwin-x64.zip  https://php-cli.jingjingxyk.com/socat-v1.8.0.1-cygwin-x64.zip

:: curl.exe -fSLo curl-8.11.1_1-win64-mingw.zip https://curl.se/windows/dl-8.11.1_1/curl-8.11.1_1-win64-mingw.zip
:: curl.exe -fSLo curl-8.11.1_1-win64arm-mingw.zip https://curl.se/windows/dl-8.11.1_1/curl-8.11.1_1-win64a-mingw.zip

endlocal

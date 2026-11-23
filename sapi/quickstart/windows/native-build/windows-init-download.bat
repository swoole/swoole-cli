setlocal


echo %~dp0
cd /d %~dp0
cd /d ..\..\..\..\

set "__PROJECT__=%cd%"
echo %cd%

md %__PROJECT__%\var\native-build\

cd /d %__PROJECT__%\var\native-build\


if not exist "strawberry-perl-5.38.2.2-64bit.msi" curl.exe -fSLo strawberry-perl-5.38.2.2-64bit.msi https://github.com/StrawberryPerl/Perl-Dist-Strawberry/releases/download/SP_53822_64bit/strawberry-perl-5.38.2.2-64bit.msi
if not exist "nasm-2.16.03-win64.zip" curl.exe -fSLo nasm-2.16.03-win64.zip https://www.nasm.us/pub/nasm/releasebuilds/2.16.03/win64/nasm-2.16.03-win64.zip
if not exist "7z2409-x64.exe" curl.exe -fSLo 7z2409-x64.exe https://www.7-zip.org/a/7z2409-x64.exe

rem homepage
rem http://www.libarchive.org/
:: if not exist "libarchive-v3.7.4-amd64.zip" curl.exe -fSLo libarchive-3.8.1.tar.gz https://github.com/libarchive/libarchive/releases/download/v3.8.1/libarchive-3.8.1.tar.gz
:: if not exist "vcpkg" git clone https://github.com/Microsoft/vcpkg.git
:: .\vcpkg\bootstrap-vcpkg.bat
:: .\vcpkg\vcpkg.exe install libarchive



:: vs2019
:: curl -Lo VisualStudioSetup.exe 'https://c2rsetup.officeapps.live.com/c2r/downloadVS.aspx?sku=community&channel=Release&version=VS2019'
:: curl -Lo VisualStudioSetup.exe 'https://aka.ms/vs/16/release/vs_community.exe'

:: vs2022
:: curl.exe -fSLo vc_redist.x64.exe https://aka.ms/vs/17/release/vc_redist.x64.exe
:: curl -fSL VisualStudioSetup.exe 'https://aka.ms/vs/17/release/vs_community.exe'
if not exist "VisualStudioSetup.exe" curl.exe -fSLo VisualStudioSetup.exe "https://c2rsetup.officeapps.live.com/c2r/downloadVS.aspx?sku=community&channel=Release&version=VS2022"

rem more info:  https://learn.microsoft.com/zh-cn/sysinternals/downloads/procdump
if not exist "Procdump.zip" curl.exe -fSLo Procdump.zip "https://download.sysinternals.com/files/Procdump.zip"


:: if not exist "jq-windows-amd64.exe" curl.exe -fSLo jq-windows-amd64.exe https://github.com/jqlang/jq/releases/download/jq-1.7.1/jq-windows-amd64.exe

:: if not exist "TEMP_PHP_RUNTIME_FILE" curl.exe https://windows.php.net/downloads/releases/releases.json | jq-windows-amd64.exe ".[\"8.4\"].[\"nts-vs17-x64\"].[\"zip\"].[\"path\"]" -r > TEMP_PHP_RUNTIME_FILE

:: set /p PHP_RUNIME_FILE=<TEMP_PHP_RUNTIME_FILE
:: echo %PHP_RUNIME_FILE%

:: https://downloads.php.net/~windows/releases/archives/php-8.4.27-1-nts-Win32-vs17-x64.zip

:: if not exist "php-nts-Win32-x64.zip" curl.exe -fSLo php-nts-Win32-x64.zip "https://windows.php.net/downloads/releases/%PHP_RUNIME_FILE%"
if not exist "php-nts-Win32-x64.zip" curl.exe -fSLo php-nts-Win32-x64.zip "https://downloads.php.net/~windows/releases/archives/php-8.4.27-1-nts-Win32-vs17-x64.zip"

if not exist "composer.phar" curl.exe -fSLo composer.phar "https://getcomposer.org/download/latest-stable/composer.phar"
if not exist "pie.phar" curl.exe -fSLo pie.phar "https://github.com/php/pie/releases/latest/download/pie.phar"
if not exist "cacert.pem" curl.exe -fSLo cacert.pem "https://curl.se/ca/cacert.pem"
if not exist "gvim_9.2.1316_x64_signed.exe" curl.exe -fSLo gvim_9.2.1316_x64_signed.exe "https://github.com/vim/vim-win32-installer/releases/download/v9.2.1316/gvim_9.2.1316_x64_signed.exe"
:: if not exist "meson-1.12.1-64.msi" curl.exe -fSLo meson-1.12.1-64.msi "https://github.com/mesonbuild/meson/releases/download/1.12.1/meson-1.12.1-64.msi"

:: Using jom instead of nmake on Windows for multi-core acceleration
if not exist "jom_1_1_7.zip" curl.exe -fSLo jom_1_1_7.zip "https://download.qt.io/official_releases/jom/jom_1_1_7.zip"

if not exist "php-sdk-binary-tools" git clone -b master --depth=1 https://github.com/php/php-sdk-binary-tools.git
:: if not exist "php-src" git clone -b php-8.4.26 --depth=1 https://github.com/php/php-src.git php-src

if not exist "all-deps.zip" curl.exe -fSLo all-deps.zip https://github.com/swoole/swoole-cli/releases/download/v6.2.5.0/all-deps.zip

:: if not exist "Microsoft.DesktopAppInstaller_8wekyb3d8bbwe.msixbundle" curl.exe -fSLo Microsoft.DesktopAppInstaller_8wekyb3d8bbwe.msixbundle https://github.com/microsoft/winget-cli/releases/download/v1.29.280/Microsoft.DesktopAppInstaller_8wekyb3d8bbwe.msixbundle

if not exist "vcpkg" git clone https://github.com/microsoft/vcpkg.git

:: Dependency Walker
if not exist "Dependencies_x64_Release.zip" curl.exe -fSLo Dependencies_x64_Release.zip "https://github.com/lucasg/Dependencies/releases/download/v1.11.1/Dependencies_x64_Release.zip"

:: .\Dependencies.exe -knowndll  ..\..\php.exe
:: dumpbin /DEPENDENTS ..\..\php.exe

endlocal

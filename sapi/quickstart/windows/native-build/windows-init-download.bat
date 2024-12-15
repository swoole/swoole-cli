setlocal


echo %~dp0
cd /d %~dp0
cd /d ..\..\..\..\

set "__PROJECT__=%cd%"
echo %cd%

md %__PROJECT__%\var\windows-build-deps\

cd /d %__PROJECT__%\var\windows-build-deps\

:: set http_proxy=http://127.0.0.1:8016
:: set https_proxy=http://127.0.0.1:8016

curl.exe -fSLo Git-2.47.1-64-bit.exe https://github.com/git-for-windows/git/releases/download/v2.47.1.windows.1/Git-2.47.1-64-bit.exe
curl.exe -fSLo strawberry-perl-5.38.2.2-64bit.msi https://github.com/StrawberryPerl/Perl-Dist-Strawberry/releases/download/SP_53822_64bit/strawberry-perl-5.38.2.2-64bit.msi
curl.exe -fSLo nasm-2.16.03-win64.zip https://www.nasm.us/pub/nasm/releasebuilds/2.16.03/win64/nasm-2.16.03-win64.zip
curl.exe -fSLo 7z2409-x64.exe https://www.7-zip.org/a/7z2409-x64.exe
curl.exe -fSLo libarchive-v3.7.4-amd64.zip https://libarchive.org/downloads/libarchive-v3.7.4-amd64.zip

:: vs2019
:: curl -Lo VisualStudioSetup.exe 'https://c2rsetup.officeapps.live.com/c2r/downloadVS.aspx?sku=community&channel=Release&version=VS2019'
:: curl -Lo VisualStudioSetup.exe 'https://aka.ms/vs/16/release/vs_community.exe'

:: vs2022
curl.exe -fSLo vc_redist.x64.exe https://aka.ms/vs/17/release/vc_redist.x64.exe
:: curl -fSL VisualStudioSetup.exe 'https://aka.ms/vs/17/release/vs_community.exe'
curl.exe -fSLo VisualStudioSetup.exe "https://c2rsetup.officeapps.live.com/c2r/downloadVS.aspx?sku=community&channel=Release&version=VS2022"


::                                   "https://windows.php.net/downloads/releases/php-8.4.1-nts-Win32-vs17-x64.zip"
curl.exe -fSLo php-nts-Win32-x64.zip "https://windows.php.net/downloads/releases/php-8.3.14-nts-Win32-vs16-x64.zip"
curl.exe -fSLo composer.phar "https://getcomposer.org/download/latest-stable/composer.phar"
curl.exe -fSLo cacert.pem "https://curl.se/ca/cacert.pem"

git clone -b master --depth=1 https://github.com/php/php-sdk-binary-tools.git
git clone -b php-8.4.1 --depth=1 https://github.com/php/php-src.git


:: with mirror
:: curl.exe -fSLo Git-2.47.1-64-bit.exe  https://php-cli.jingjingxyk.com/Git-2.47.1-64-bit.exe
:: curl.exe -fSLo 7z2409-x64.exe  https://php-cli.jingjingxyk.com/7z2409-x64.exe
:: curl.exe -fSLo nasm-2.16.03-win64.zip  https://php-cli.jingjingxyk.com/nasm-2.16.03-win64.zip



endlocal

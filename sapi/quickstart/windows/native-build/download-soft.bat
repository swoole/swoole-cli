setlocal

curl.exe -Lo Git-2.47.1-64-bit.exe https://github.com/git-for-windows/git/releases/download/v2.47.1.windows.1/Git-2.47.1-64-bit.exe
rem git mirror
rem curl.exe -Lo Git-2.47.1-64-bit.exe https://php-cli.jingjingxyk.com/Git-2.47.1-64-bit.exe

start /wait .\Git-2.47.1-64-bit.exe /VERYSILENT /NORESTART /NOCANCEL /SP- /CLOSEONEXIT=1 /DIR="C:\Program Files\Git"

set "PATH=%PATH%;C:\Program Files\Git\bin;"
git config --global core.autocrlf false
git config --global core.eol lf

curl.exe -Lo strawberry-perl-5.38.2.2-64bit.msi https://github.com/StrawberryPerl/Perl-Dist-Strawberry/releases/download/SP_53822_64bit/strawberry-perl-5.38.2.2-64bit.msi
curl.exe -Lo strawberry-perl-5.38.2.2-64bit.msi https://php-cli.jingjingxyk.com/strawberry-perl-5.38.2.2-64bit.msi

curl.exe -Lo vc_redist.x64.exe https://aka.ms/vs/17/release/vc_redist.x64.exe

curl.exe -Lo VisualStudioSetup.exe "https://c2rsetup.officeapps.live.com/c2r/downloadVS.aspx?sku=community&channel=Release&version=VS2022"

# curl.exe -Lo nasm-2.16.03-win64.zip https://github.com/jingjingxyk/swoole-cli/releases/download/t-v0.0.3/nasm-2.16.03-win64.zip
curl.exe -Lo nasm-2.16.03-win64.zip https://www.nasm.us/pub/nasm/releasebuilds/2.16.03/win64/nasm-2.16.03-win64.zip
curl.exe -Lo nasm-2.16.03-win64.zip https://php-cli.jingjingxyk.com/nasm-2.16.03-win64.zip



echo %~dp0
cd /d %~dp0
cd /d .\..\..\..\..\

set "__PROJECT__=%cd%"
set "PATH=%PATH%;%__PROJECT__%\nasm\;C:\Strawberry\perl\bin;C:\Program Files\Git\bin;"

git clone -b build_native_php https://github.com/jingjingxyk/swoole-cli.git
git clone -b master --depth=1 https://github.com/php/php-sdk-binary-tools.git
git clone -b php-8.4.1 --depth=1 https://github.com/php/php-src.git

msiexec /i strawberry-perl-5.38.2.2-64bit.msi  /passive
.\vc_redist.x64.exe /install /passive /norestart


.\VisualStudioSetup.exe ^
--locale en-US ^
--add Microsoft.VisualStudio.Component.VC.Tools.x86.x64 ^
--add Microsoft.VisualStudio.Component.VC.Modules.x86.x64 ^
--add Microsoft.VisualStudio.Component.VC.CMake.Project ^
--add Microsoft.VisualStudio.Component.Roslyn.Compiler ^
--add Microsoft.VisualStudio.Component.CoreBuildTools ^
--add Microsoft.VisualStudio.Component.Windows10SDK.20348	^
--add Microsoft.VisualStudio.Component.Windows10SDK ^
--add Microsoft.VisualStudio.Component.Windows11SDK.22000   ^
--add Microsoft.Component.VC.Runtime.UCRTSDK	^
--add Microsoft.Component.MSBuild ^
--add Microsoft.VisualStudio.Workload.MSBuildTools ^
--add Microsoft.VisualStudio.Workload.NativeDesktop ^
--passive  --force --norestart


cd php-sdk-binary-tools
.\phpsdk-vs17-x64.bat

unzip nasm-2.16.03-win64.zip
mv  nasm-2.16.03 nasm


cd php-src


perl -v
php -v
nasm -v
git version
curl -V


curl.exe -Lo npp.8.6.7.Installer.x64.exe https://github.com/notepad-plus-plus/notepad-plus-plus/releases/download/v8.6.7/npp.8.6.7.Installer.x64.exe
curl.exe -Lo npp.8.6.7.Installer.x64.exe https://php-cli.jingjingxyk.com/npp.8.6.7.Installer.x64.exe
.\npp.8.6.7.Installer.x64.exe /VERYSILENT /NORESTART /NOCANCEL /SP


endlocal


rem "C:\Program Files\Microsoft Visual Studio\2022\Community\VC\Auxiliary\Build\vcvarsall.bat" amd64

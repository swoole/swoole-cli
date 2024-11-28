Invoke-WebRequest -Uri https://github.com/git-for-windows/git/releases/download/v2.47.1.windows.1/Git-2.47.1-64-bit.exe -OutFile .\Git-2.47.1-64-bit.exe

start /wait .\Git-2.47.1-64-bit.exe /VERYSILENT /NORESTART /NOCANCEL /SP- /CLOSEONEXIT=1 /DIR="C:\Program Files\Git"

git clone -b master --depth=1 https://github.com/php/php-sdk-binary-tools.git
git clone -b php-8.4.1 --depth=1 https://github.com/php/php-src.git

curl.exe -Lo strawberry-perl-5.38.2.2-64bit.msi https://github.com/StrawberryPerl/Perl-Dist-Strawberry/releases/download/SP_53822_64bit/strawberry-perl-5.38.2.2-64bit.msi

curl.exe -Lo vc_redist.x64.exe https://aka.ms/vs/17/release/vc_redist.x64.exe

curl.exe -Lo VisualStudioSetup.exe "https://c2rsetup.officeapps.live.com/c2r/downloadVS.aspx?sku=community&channel=Release&version=VS2022"

curl.exe -Lo nasm-2.16.03-win64.zip https://github.com/jingjingxyk/swoole-cli/releases/download/t-v0.0.3/nasm-2.16.03-win64.zip

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
.\phpsdk-vc17-x64.bat

cd php-src


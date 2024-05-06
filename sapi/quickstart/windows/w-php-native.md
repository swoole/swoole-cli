

https://aka.ms/vs/17/release/vc_redist.x64.exe

# 下载 visual studio 安装器
https://c2rsetup.officeapps.live.com/c2r/downloadVS.aspx?sku=community&channel=Release&version=VS2022
https://c2rsetup.officeapps.live.com/c2r/downloadVS.aspx?sku=community&channel=Release&version=VS2019

https://aka.ms/vs/17/release/vs_buildtools.exe


# 使用命令行安装
https://learn.microsoft.com/zh-cn/visualstudio/install/use-command-line-parameters-to-install-visual-studio?view=vs-2022

# uninstall ^

VisualStudioSetup.exe ^
--locale en-US ^
--add Microsoft.VisualStudio.Component.VC.Tools.x86.x64 ^
--add Microsoft.Component.MSBuild ^
--add Microsoft.VisualStudio.Component.Roslyn.Compiler ^
--add Microsoft.Component.MSBuild ^
--add Microsoft.VisualStudio.Component.CoreBuildTools ^
--add Microsoft.VisualStudio.Workload.MSBuildTools ^
--quiet --force  ^
--path install="C:\VS" ^
--path cache="C:\VS\cache" ^
--path shared="C:\VS\shared" ^
--channelId VisualStudio.16.Release ^
--norestart --force ^




git clone -b php-8.3.6     --depth=1 https://github.com/php/php-src.git
git clone -b php-sdk-2.2.0 --depth=1 https://github.com/php/php-sdk-binary-tools.git

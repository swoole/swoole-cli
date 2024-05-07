
Microsoft Visual C++ 运行时库
https://aka.ms/vs/17/release/vc_redist.x64.exe

# 下载 visual studio 安装器
https://c2rsetup.officeapps.live.com/c2r/downloadVS.aspx?sku=community&channel=Release&version=VS2022
https://c2rsetup.officeapps.live.com/c2r/downloadVS.aspx?sku=community&channel=Release&version=VS2019

https://aka.ms/vs/17/release/vs_buildtools.exe


curl -Lo VisualStudioSetup.exe 'https://c2rsetup.officeapps.live.com/c2r/downloadVS.aspx?sku=community&channel=Release&version=VS2022'
curl -Lo VisualStudioSetup.exe 'https://aka.ms/vs/17/release/vs_community.exe'
curl -Lo vs_buildtools.exe 'https://aka.ms/vs/17/release/vs_buildtools.exe'


# 使用命令行参数安装、更新和管理 Visual Studio
https://learn.microsoft.com/zh-cn/visualstudio/install/use-command-line-parameters-to-install-visual-studio?view=vs-2022


VisualStudioSetup.exe --locale en-US --add Microsoft.VisualStudio.Component.VC.Tools.x86.x64 --add Microsoft.Component.MSBuild --add Microsoft.VisualStudio.Component.Roslyn.Compiler --add Microsoft.Component.MSBuild --add Microsoft.VisualStudio.Component.CoreBuildTools --add Microsoft.VisualStudio.Workload.MSBuildTools  --path install="C:\VS" --path cache="C:\VS\cache" --path shared="C:\VS\shared"
--quiet --force --norestart
--channelId VisualStudio.16.Release ^


vs_buildtools.exe  --quiet --force  --norestart

git clone -b php-8.3.6     --depth=1 https://github.com/php/php-src.git
git clone -b php-sdk-2.2.0 --depth=1 https://github.com/php/php-sdk-binary-tools.git

## 通过命令行使用 MSVC 工具集
    https://learn.microsoft.com/zh-cn/cpp/build/building-on-the-command-line?view=msvc-170

## 通过命令行使用 MSBuild
    https://learn.microsoft.com/zh-cn/cpp/build/msbuild-visual-cpp?view=msvc-170

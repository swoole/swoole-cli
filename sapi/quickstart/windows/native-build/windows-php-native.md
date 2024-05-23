
Microsoft Visual C++ 运行时库
https://aka.ms/vs/17/release/vc_redist.x64.exe

# 下载 visual studio 安装器
https://c2rsetup.officeapps.live.com/c2r/downloadVS.aspx?sku=community&channel=Release&version=VS2022
https://c2rsetup.officeapps.live.com/c2r/downloadVS.aspx?sku=community&channel=Release&version=VS2019

https://aka.ms/vs/17/release/vs_buildtools.exe


curl -Lo VisualStudioSetup.exe 'https://c2rsetup.officeapps.live.com/c2r/downloadVS.aspx?sku=community&channel=Release&version=VS2022'
curl -Lo VisualStudioSetup.exe 'https://aka.ms/vs/17/release/vs_community.exe'
curl -Lo vs_buildtools.exe 'https://aka.ms/vs/17/release/vs_buildtools.exe'


## 使用命令行参数安装、更新和管理 Visual Studio
https://learn.microsoft.com/zh-cn/visualstudio/install/use-command-line-parameters-to-install-visual-studio?view=vs-2022


##  Visual Studio 生成工具组件目录
https://learn.microsoft.com/zh-cn/visualstudio/install/workload-component-id-vs-build-tools?view=vs-2022

VisualStudioSetup.exe --locale en-US --add Microsoft.VisualStudio.Component.VC.Tools.x86.x64 --add Microsoft.Component.MSBuild --add Microsoft.VisualStudio.Component.Roslyn.Compiler --add Microsoft.Component.MSBuild --add Microsoft.VisualStudio.Component.CoreBuildTools --add Microsoft.VisualStudio.Workload.MSBuildTools --add Microsoft.VisualStudio.Component.Windows11SDK.22000	 --add Microsoft.VisualStudio.Component.Windows10SDK.20348	 --add Microsoft.VisualStudio.Component.Windows10SDK   --path install="C:\VS" --path cache="C:\VS\cache" --path shared="C:\VS\shared"
--quiet --force --norestart
--channelId VisualStudio.16.Release ^


vs_buildtools.exe  --quiet --force  --norestart


## 通过命令行使用 MSVC 工具集
    https://learn.microsoft.com/zh-cn/cpp/build/building-on-the-command-line?view=msvc-170

## 通过命令行使用 MSBuild
    https://learn.microsoft.com/zh-cn/cpp/build/msbuild-visual-cpp?view=msvc-170


// C:\Program Files\Microsoft Visual Studio\2022\Enterprise //
// C:\Program Files\Microsoft Visual Studio\2022\Community //
cl /?


## Microsoft Visual C++ 可再发行程序包最新支持的下载
    https://learn.microsoft.com/zh-cn/cpp/windows/latest-supported-vc-redist?view=msvc-170


Windows SDK
https://developer.microsoft.com/en-us/windows/downloads/windows-sdk/

消息编译器是 Windows SDK 的一部分
消息编译器命令行在这里描述：MC.EXE

Visual Studio 教程 | C++
https://learn.microsoft.com/zh-cn/cpp/get-started/?view=msvc-170

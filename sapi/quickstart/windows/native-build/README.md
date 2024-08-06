# 构建原生 PHP

    1. 准备 msys2 环境
    2. msys2 环境下 下载软件包 、PHP 运行时 、PHP SDK 等
    3. CMD 环境执行构建

## 一 、[msys2 环境 下载软件包 、PHP 运行时 、PHP SDK 等 ](msys2/README.md)

## 二、CMD 环境构建

```bat

# 安装  vc 运行时 （ 可跳过 ）
sapi\quickstart\windows\native-build\install-vc-runtime.bat

sapi\quickstart\windows\native-build\install-visualstudio-2019.bat

sapi\quickstart\windows\native-build\install-deps-soft.bat


# vs2019

cmd /k "C:\Program Files (x86)\Microsoft Visual Studio\2019\Community\VC\Auxiliary\Build\vcvarsall.bat" amd64

# start /B
# cmd /c

sapi\quickstart\windows\native-build\library\zlib.bat

sapi\quickstart\windows\native-build\library\openssl.bat


cmd /k sapi\quickstart\windows\native-build\native-build-php-sdk-vs2019.bat

:: phpsdk_deps -u
:: phpsdk_buildtree phpdev

sapi\quickstart\windows\native-build\native-build-php-config.bat

sapi\quickstart\windows\native-build\native-build-php-build.bat

sapi\quickstart\windows\native-build\native-build-php-release.bat

sapi\quickstart\windows\native-build\native-build-php-archive.bat


```

## 实验 vs2022 环境构建

```bat
# 自动打开指定文件夹
start C:\msys64\home\Administrator\swoole-cli
start C:\msys64\home\Administrator\swoole-cli\php-src\
start C:\msys64\home\Administrator\swoole-cli\php-src\x64\Release

sapi\quickstart\windows\native-build\install-visualstudio-2022.bat

# vs2022
"C:\Program Files\Microsoft Visual Studio\2022\Community\VC\Auxiliary\Build\vcvarsall.bat" amd64

sapi\quickstart\windows\native-build\native-build-php-sdk-vs2022.bat

```

## 构建window  PHP 工具 和 参考

[ download windows PHP ](https://windows.php.net/download#php-8.2)

[windows build php 步骤](https://wiki.php.net/internals/windows/stepbystepbuild_sdk_2)

[internals/windows/libs](https://wiki.php.net/internals/windows/libs)

```shell

git config core.ignorecase false # 设置 Git 在 Windows 上也区分大小写

```

Latest VC++
https://learn.microsoft.com/en-AU/cpp/windows/latest-supported-vc-redist

7zip
https://7-zip.org/

visualstudio
https://visualstudio.microsoft.com/zh-hans/downloads/

windows-sdk
https://developer.microsoft.com/en-us/windows/downloads/windows-sdk/

Windows PowerShell ISE 文本编辑器

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

## 使用命令行参数安装、更新和管理 Visual Studio

https://learn.microsoft.com/zh-cn/visualstudio/install/use-command-line-parameters-to-install-visual-studio?view=vs-2022

## Visual Studio 生成工具组件目录

https://learn.microsoft.com/zh-cn/visualstudio/install/workload-component-id-vs-build-tools?view=vs-2022

```shell


VisualStudioSetup.exe
--locale en-US
--add Microsoft.VisualStudio.Component.VC.Tools.x86.x64
--add Microsoft.Component.MSBuild
--add Microsoft.VisualStudio.Component.Roslyn.Compiler
--add Microsoft.Component.MSBuild
--add Microsoft.VisualStudio.Component.CoreBuildTools
--add Microsoft.VisualStudio.Workload.MSBuildTools
--add Microsoft.VisualStudio.Component.Windows11SDK.22000
--add Microsoft.VisualStudio.Component.Windows10SDK.20348
--add Microsoft.VisualStudio.Component.Windows10SDK
--path install="C:\VS" --path cache="C:\VS\cache" --path shared="C:\VS\shared"
--quiet --force --norestart
--channelId VisualStudio.16.Release ^

vs_buildtools.exe --quiet --force --norestart

```

Microsoft Visual C++ 运行时库
https://learn.microsoft.com/zh-cn/cpp/windows/latest-supported-vc-redist?view=msvc-170
https://aka.ms/vs/17/release/vc_redist.x64.exe

## 下载 visual studio 安装器

    https://c2rsetup.officeapps.live.com/c2r/downloadVS.aspx?sku=community&channel=Release&version=VS2022
    https://c2rsetup.officeapps.live.com/c2r/downloadVS.aspx?sku=community&channel=Release&version=VS2019

    https://aka.ms/vs/17/release/vs_buildtools.exe

    curl -Lo VisualStudioSetup.exe 'https://c2rsetup.officeapps.live.com/c2r/downloadVS.aspx?sku=community&channel=Release&version=VS2022'
    curl -Lo VisualStudioSetup.exe 'https://aka.ms/vs/17/release/vs_community.exe'
    curl -Lo vs_buildtools.exe 'https://aka.ms/vs/17/release/vs_buildtools.exe'

```shell
# 编译cpp
cl /EHsc /MT test-vc.cpp /link LIBCMT.LIB /NODEFAULTLIB:msvcrt.lib

# 查看连接信息

dumpbin /DEPENDENTS test-vc.exe

```

## 参考文档

1. [通过命令行使用 MSVC 工具集](https://learn.microsoft.com/zh-cn/cpp/build/building-on-the-command-line?view=msvc-170)
1. [从命令行使用 Microsoft C++ 工具集](https://learn.microsoft.com/en-us/cpp/build/building-on-the-command-line?view=msvc-170#download-and-install-the-tools)
1. [通过命令行使用 MSBuild](https://learn.microsoft.com/zh-cn/cpp/build/msbuild-visual-cpp?view=msvc-1700)
1. [Microsoft Visual C++ 最新运行时库](https://learn.microsoft.com/zh-cn/cpp/windows/latest-supported-vc-redist?view=msvc-170)
1. [Visual Studio 生成工具组件目录](https://learn.microsoft.com/zh-cn/visualstudio/install/workload-component-id-vs-build-tools?view=vs-2022)
1. [使用命令行参数安装、更新和管理 Visual Studio](https://learn.microsoft.com/zh-cn/visualstudio/install/use-command-line-parameters-to-install-visual-studio?view=vs-2022)
1. [Windows SDK](https://developer.microsoft.com/en-us/windows/downloads/windows-sdk/)
1. [windows 环境下 构建 php 步骤](https://wiki.php.net/internals/windows/stepbystepbuild_sdk_2)
1. [VisualStudio 导入或导出安装配置](https://learn.microsoft.com/zh-cn/visualstudio/install/import-export-installation-configurations?view=vs-2022)
1. [Visual Studio 2019 版本 16.11 发行说明](https://learn.microsoft.com/zh-cn/visualstudio/releases/2019/release-notes)
1. [Visual Studio 2022 版本 17.9 发行说明](https://learn.microsoft.com/zh-cn/visualstudio/releases/2022/release-notes)
1. [MSVC 如何将清单嵌入到 C/C++ 应用程序中](https://learn.microsoft.com/zh-cn/cpp/build/understanding-manifest-generation-for-c-cpp-programs?view=msvc-170)
1. [Visual Studio 教程 | C++](https://learn.microsoft.com/zh-cn/cpp/get-started/?view=msvc-170)
1. [7zip](https://7-zip.org/)
1. [Visual Studio 许可证目录](https://visualstudio.microsoft.com/zh-hans/license-terms/)
1. [windows环境 使用ssh](https://learn.microsoft.com/zh-cn/windows-server/administration/openssh/openssh_install_firstuse)
1. [MSVC链接器选项](https://learn.microsoft.com/zh-cn/cpp/build/reference/linker-options?view=msvc-170)
1. [MSVC Mt.exe](https://learn.microsoft.com/en-us/windows/win32/sbscs/mt-exe?redirectedfrom=MSDN)
1. [/MD、/MT、/LD（使用运行时库）](https://learn.microsoft.com/zh-cn/cpp/build/reference/md-mt-ld-use-run-time-library?view=msvc-170)
1. [Install PowerShell on Windows, Linux, and macOS](https://learn.microsoft.com/en-us/powershell/scripting/install/installing-powershell?view=powershell-7.4)
1. [Sysinternals Utilities Index](https://learn.microsoft.com/en-us/sysinternals/downloads/)


# 构建原生 PHP

    1. 准备 msys2 环境
    2. msys2 环境下 下载软件包 、PHP 运行时 、PHP SDK 等
    3. CMD 环境执行构建

## 一 、[msys2 环境 下载软件包 、PHP 运行时 、PHP SDK 等 ](msys2/README.md)

## 二、CMD 环境构建

```bat

sapi\quickstart\windows\native-build\install-visualstudio.bat

sapi\quickstart\windows\native-build\install-deps-soft.bat


# vs2019

"C:\Program Files (x86)\Microsoft Visual Studio\2019\Community\VC\Auxiliary\Build\vcvarsall.bat" amd64

# start /B
# cmd /c

sapi\quickstart\windows\native-build\library\zlib.bat

sapi\quickstart\windows\native-build\library\openssl.bat


sapi\quickstart\windows\native-build\native-build-php-sdk-vs2019.bat


sapi\quickstart\windows\native-build\native-build-php-config.bat

sapi\quickstart\windows\native-build\native-build-php-build.bat

sapi\quickstart\windows\native-build\native-build-php-build-release.bat

sapi\quickstart\windows\native-build\native-build-php-archive.bat


```

## 实验 vs2022 环境构建

```bat
# vs2022
"C:\Program Files\Microsoft Visual Studio\2022\Community\VC\Auxiliary\Build\vcvarsall.bat" amd64

sapi\quickstart\windows\native-build\native-build-php-sdk-vs2022.bat

```

## 构建window  PHP 工具 和 参考

[ download windows PHP ](https://windows.php.net/download#php-8.2)

[windows build php 步骤](https://wiki.php.net/internals/windows/stepbystepbuild)

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
--path install="C:\VS" --path cache="C:\VS\cache" --path shared="C:
\VS\shared"
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

    curl -Lo
    VisualStudioSetup.exe 'https://c2rsetup.officeapps.live.com/c2r/downloadVS.aspx?sku=community&channel=Release&version=VS2022'
    curl -Lo VisualStudioSetup.exe 'https://aka.ms/vs/17/release/vs_community.exe'
    curl -Lo vs_buildtools.exe 'https://aka.ms/vs/17/release/vs_buildtools.exe'

## 参考文档

1. [通过命令行使用 MSVC 工具集](https://learn.microsoft.com/zh-cn/cpp/build/building-on-the-command-line?view=msvc-170)
1. [通过命令行使用 MSBuild](https://learn.microsoft.com/zh-cn/cpp/build/msbuild-visual-cpp?view=msvc-1700)
1. [Windows SDK](https://developer.microsoft.com/en-us/windows/downloads/windows-sdk/)
1. [windows 环境下 构建 php 步骤](https://wiki.php.net/internals/windows/stepbystepbuild_sdk_2)
1. [VisualStudio 导入或导出安装配置](https://learn.microsoft.com/zh-cn/visualstudio/install/import-export-installation-configurations?view=vs-2022)
1. [Visual Studio 2019 版本 16.11 发行说明](https://learn.microsoft.com/zh-cn/visualstudio/releases/2019/release-notes)
1. [Visual Studio 2022 版本 17.9 发行说明](https://learn.microsoft.com/zh-cn/visualstudio/releases/2022/release-notes)




可以借助 MSYS2 环境 和 CMD 环境 进行构建

1. MSYS2 环境 用于下载软件     (msys2 集成了 Mingw 和 Cygwin ，同时还提供了包管理工具 `pacman`)
2. CMD 环境 安装Visual Studio
3. CMD 环境 执行编译

## msys2下载软件

1. 下载 msys2  [msys2](https://www.msys2.org/])
   > 浏览器打开,自动给下载 msys2： https://mirror.msys2.org/distrib/x86_64/msys2-x86_64-20240507.exe
1. 安装 msys2
   > 双击 `msys2-x86_64-20240507.exe ` 进行安装
1. msys2安装软件
   ```shell
    pacman -Syy --noconfirm git curl
   ```
1. msys2 环境下使用curl 下载软件
    ```shell
   # 下载 vs2022

   # 方式一
   curl -Lo VisualStudioSetup.exe 'https://c2rsetup.officeapps.live.com/c2r/downloadVS.aspx?sku=community&channel=Release&version=VS2022'
   # 方式二
   curl -Lo VisualStudioSetup.exe 'https://aka.ms/vs/17/release/vs_community.exe'

   ```

   ```shell
   # 下载 php 源码
   git clone -b php-8.3.6     --depth=1 https://github.com/php/php-src.git

   # 下载 php-sdk for windows
   # git clone -b php-sdk-2.2.0 --depth=1 https://github.com/php/php-sdk-binary-tools.git
   git clone -b master --depth=1 https://github.com/php/php-sdk-binary-tools.git

   ```

## CMD 环境 安装VisualStudio

1. [使用命令行参数安装、更新和管理 Visual Studio](https://learn.microsoft.com/zh-cn/visualstudio/install/use-command-line-parameters-to-install-visual-studio?view=vs-2022)
1. [Visual Studio 生成工具组件目录](https://learn.microsoft.com/zh-cn/visualstudio/install/workload-component-id-vs-build-tools?view=vs-2022)

> 使用命令行快速安装 VisualStudio 组件

```bat
cd c:\msys64\home\Administrator\

VisualStudioSetup.exe ^
--locale en-US ^
--add Microsoft.VisualStudio.Component.VC.Tools.x86.x64 ^
--add Microsoft.Component.MSBuild ^
--add Microsoft.VisualStudio.Component.Roslyn.Compiler ^
--add Microsoft.Component.MSBuild ^
--add Microsoft.VisualStudio.Component.CoreBuildTools ^
--add Microsoft.VisualStudio.Workload.MSBuildTools ^
--add Microsoft.VisualStudio.Component.Windows11SDK.22000   ^
--add Microsoft.VisualStudio.Component.Windows10SDK.20348	^
--add Microsoft.VisualStudio.Component.Windows10SDK ^
--passive  --force --norestart
```

## CMD 环境 编译构建

```bat
cd c:\msys64\home\Administrator\php-sdk-binary-tools
phpsdk-vs17-x64.bat

```

```bat
cd c:\msys64\home\Administrator\php-src
buildconf.bat
configure.bat --help
configure.bat --disable-all --enable-cli --enable-static=yes --enable-shared=no
nmake


x64\Release_TS\php.exe -v
x64\Release_TS\php.exe -m

```

## 参考文档

1. [通过命令行使用 MSVC 工具集](https://learn.microsoft.com/zh-cn/cpp/build/building-on-the-command-line?view=msvc-170)
1. [通过命令行使用 MSBuild](https://learn.microsoft.com/zh-cn/cpp/build/msbuild-visual-cpp?view=msvc-1700)
1. [Windows SDK](https://developer.microsoft.com/en-us/windows/downloads/windows-sdk/)
1. [windows 环境下 构建 php 步骤](https://wiki.php.net/internals/windows/stepbystepbuild_sdk_2)
1. [VisualStudio 导入或导出安装配置](https://learn.microsoft.com/zh-cn/visualstudio/install/import-export-installation-configurations?view=vs-2022)
1. [Visual Studio 2019 版本 16.11 发行说明](https://learn.microsoft.com/zh-cn/visualstudio/releases/2019/release-notes)
1. [Visual Studio 2022 版本 17.9 发行说明](https://learn.microsoft.com/zh-cn/visualstudio/releases/2022/release-notes)

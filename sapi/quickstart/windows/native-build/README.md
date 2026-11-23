# 构建原生 PHP

## 二、CMD 环境构建

> bat 脚本不能包含中文

> nmake并行编译 使用NMake命令，并在其后加上/MP选项

> `echo. >>` makefile 给 makefile 增加一空行

> link.exe 链接静态库 `link /OUT:myprogram.exe /LIBPATH:C:\libs myprogram.obj mylib.lib`

```

git config --global core.autocrlf false
git config --global core.eol lf
git config --global core.ignorecase false


```

```bash

.\sapi\quickstart\windows\native-build\windows-init-download.bat
.\sapi\quickstart\windows\native-build\windows-init-install.bat
.\sapi\quickstart\windows\native-build\windows-init-install-vs-tools.bat

.\sapi\scripts\windows-native\config.bat
.\sapi\scripts\windows-native\x_custom_config.bat
.\sapi\scripts\windows-native\\build.bat

```

```powershell
 cmd /c .\sapi\quickstart\windows\native-build\windows-init-download.bat
 cmd /c .\sapi\quickstart\windows\native-build\windows-init-install.bat
 cmd /c .\sapi\quickstart\windows\native-build\windows-init-install-vs-tools.bat

 cmd /c .\sapi\scripts\windows-native\config.bat
 cmd /c .\sapi\scripts\windows-native\x_custom_config.bat
 cmd /c .\sapi\scripts\windows-native\\build.bat

```

```bat



:: vs2022

%comspec% /k "d:\Program Files\Microsoft Visual Studio\2022\Community\VC\Auxiliary\Build\vcvarsall.bat" amd64

cmd /k "d:\Program Files\Microsoft Visual Studio\2022\Community\VC\Auxiliary\Build\vcvarsall.bat" amd64

:: start /B
:: cmd /c

:: powershell vs dev
::  C:\Windows\SysWOW64\WindowsPowerShell\v1.0\powershell.exe -noe -c "&{Import-Module """D:\vs\Common7\Tools\Microsoft.VisualStudio.DevShell.dll"""; Enter-VsDevShell 79dcbe64}"

%comspec% /k "c:\vs\Common7\Tools\VsDevCmd.bat"
%comspec% /k "c:\vs\VC\Auxiliary\Build\vcvars64.bat"
%comspec% /k "c:\vs\VC\Auxiliary\Build\vcvarsamd64_x86.bat"
%ProgramFiles(x86)%
%USERPROFILE%
%NUMBER_OF_PROCESSORS%

:: phpsdk_deps -u
:: phpsdk_buildtree phpdev



cd .\var\windows-build-deps\php-src\
buildconf.bat -f
configure --help

configure.bat ^
--with-php-build="c:\php-cli" ^
--with-extra-includes='' ^
--with-extra-libs='' ^
--disable-all         --disable-cgi      --enable-cli   ^
--enable-sockets      --enable-ctype     --enable-pdo    --enable-phar  ^
--enable-filter ^
--enable-xmlreader   --enable-xmlwriter ^
--enable-tokenizer

::--enable-cli-win32

nmake /E php.exe


dumpbin /DEPENDENTS ".\x64\Release_TS\php.exe"

:: in powershell
cmd /c dumpbin /DEPENDENTS ".\x64\Release_TS\php.exe"
```

```shell
# 下载 vs2022

# 方式一
curl -Lo VisualStudioSetup.exe 'https://c2rsetup.officeapps.live.com/c2r/downloadVS.aspx?sku=community&channel=Release&version=VS2022'

curl -Lo VisualStudioSetup.exe 'https://c2rsetup.officeapps.live.com/c2r/downloadVS.aspx?sku=community&channel=stable&version=VS18'

# 方式二
curl -Lo VisualStudioSetup.exe 'https://aka.ms/vs/17/release/vs_community.exe'
curl -Lo VisualStudioSetup.exe 'https://aka.ms/vs/18/release/vs_community.exe'


```

## 实验 vs2022 环境构建

```bat
# 自动打开指定文件夹
start C:\msys64\home\Administrator\swoole-cli
start C:\msys64\home\Administrator\swoole-cli\php-src\
start C:\msys64\home\Administrator\swoole-cli\php-src\x64\Release

# vs2022
"C:\Program Files\Microsoft Visual Studio\2022\Community\VC\Auxiliary\Build\vcvarsall.bat" amd64


```

## 构建window  PHP 工具 和 参考

[internals/windows/libs](https://wiki.php.net/internals/windows/libs)
[download windows PHP ](https://windows.php.net/download#php-8.2)
[windows build php 步骤](https://wiki.php.net/internals/windows/stepbystepbuild)
[windows build php 步骤](https://wiki.php.net/internals/windows/stepbystepbuild_sdk_2)
[Latest VC++](https://learn.microsoft.com/en-AU/cpp/windows/latest-supported-vc-redist)
[7zip](https://7-zip.org/)
[visualstudio](https://visualstudio.microsoft.com/zh-hans/downloads/)
[windows-sdk](https://developer.microsoft.com/en-us/windows/downloads/windows-sdk/)
[通过命令行使用 MSVC 工具集](https://learn.microsoft.com/zh-cn/cpp/build/building-on-the-command-line?view=msvc-170)
[Microsoft Visual C++ 可再发行程序包最新支持的下载](https://learn.microsoft.com/zh-cn/cpp/windows/latest-supported-vc-redist?view=msvc-170)
[Visual Studio 教程 | C++](https://learn.microsoft.com/zh-cn/cpp/get-started/?view=msvc-170)
[使用命令行参数安装、更新和管理 Visual Studio](https://learn.microsoft.com/zh-cn/visualstudio/install/use-command-line-parameters-to-install-visual-studio?view=vs-2022)
[Visual Studio 开发人员命令提示和开发人员 PowerShell](https://learn.microsoft.com/zh-cn/visualstudio/ide/reference/command-prompt-powershell?view=vs-2022&redirectedfrom=MSDN)

## 通过命令行使用 MSBuild

    https://learn.microsoft.com/zh-cn/cpp/build/msbuild-visual-cpp?view=msvc-170

    // C:\Program Files\Microsoft Visual Studio\2022\Enterprise //
    // C:\Program Files\Microsoft Visual Studio\2022\Community //
    cl /?

Windows SDK
https://developer.microsoft.com/en-us/windows/downloads/windows-sdk/

消息编译器是 Windows SDK 的一部分
消息编译器命令行在这里描述：MC.EXE

## Visual Studio 生成工具组件目录

https://learn.microsoft.com/zh-cn/visualstudio/install/workload-component-id-vs-build-tools?view=vs-2022

> MSVC 命令行工具使用 PATH、TMP、INCLUDE、LIB 和 LIBPATH 环境变量

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
--path install="C:\VS" --path cache="C:\VS-cache" --path shared="C:\VS-shared"
--quiet --force --norestart
--channelId VisualStudio.16.Release ^

vs_buildtools.exe --quiet --force --norestart

# 导出配置
VisualStudioSetup.exe export 	--passive  --force
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
# https://learn.microsoft.com/zh-cn/cpp/build/walkthrough-creating-and-using-a-static-library-cpp?view=msvc-170

cl /c /EHsc MathLibrary.cpp

cl /EHsc /MT test-vc.cpp /link LIBCMT.LIB /NODEFAULTLIB:msvcrt.lib

LINK first.obj second.obj third.obj /OUT:filename.exe

# 查看连接信息

dumpbin /DEPENDENTS test-vc.exe

```

## 参考文档

1. [virtualstudio 命令行安装](https://learn.microsoft.com/zh-cn/visualstudio/install/use-command-line-parameters-to-install-visual-studio?view=vs-2022)
1. [使用命令行参数安装、更新和管理 Visual Studio](https://learn.microsoft.com/zh-cn/visualstudio/install/use-command-line-parameters-to-install-visual-studio?view=vs-2022)
1. [通过命令行使用 MSVC 工具集](https://learn.microsoft.com/zh-cn/cpp/build/building-on-the-command-line?view=msvc-170)
1. [从命令行使用 Microsoft C++ 工具集](https://learn.microsoft.com/en-us/cpp/build/building-on-the-command-line?view=msvc-170#download-and-install-the-tools)
1. [通过命令行使用 MSBuild](https://learn.microsoft.com/zh-cn/cpp/build/msbuild-visual-cpp?view=msvc-1700)
1. [Microsoft Visual C++ 最新运行时库](https://learn.microsoft.com/zh-cn/cpp/windows/latest-supported-vc-redist?view=msvc-170)
1. [Visual Studio 生成工具组件目录](https://learn.microsoft.com/zh-cn/visualstudio/install/workload-component-id-vs-build-tools?view=vs-2022)
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
1. [curl 8.11.1 for Windows](https://curl.se/windows/)
1. [windows php release ](https://windows.php.net/downloads/releases/archives/)
1. [通过命令行使用 MSVC 工具集](https://learn.microsoft.com/zh-cn/cpp/build/building-on-the-command-line?view=msvc-170)
1. [通过命令行使用 MSBuild](https://learn.microsoft.com/zh-cn/cpp/build/msbuild-visual-cpp?view=msvc-1700)
1. [Windows SDK](https://developer.microsoft.com/en-us/windows/downloads/windows-sdk/)
1. [windows 环境下 构建 php 步骤](https://wiki.php.net/internals/windows/stepbystepbuild_sdk_2)
1. [VisualStudio 导入或导出安装配置](https://learn.microsoft.com/zh-cn/visualstudio/install/import-export-installation-configurations?view=vs-2022)
1. [Visual Studio 2019 版本 16.11 发行说明](https://learn.microsoft.com/zh-cn/visualstudio/releases/2019/release-notes)
1. [Visual Studio 2022 版本 17.9 发行说明](https://learn.microsoft.com/zh-cn/visualstudio/releases/2022/release-notes)

常用命令 说明
MSBuild 生成项目或解决方案
dotnet .NET CLI 命令
dotnet run .NET CLI 命令
clrver 用于 CLR 的 .NET Framework 工具
ildasm 用于反汇编程序的 .NET Framework 工具
CL C/C++ 编译工具
NMAKE C/C++ 编译工具
LIB C/C++ 生成工具
DUMPBIN C/C++ 生成工具

```bash


# https://github.com/notepad-plus-plus/notepad-plus-plus/
test -f npp.8.6.7.Installer.x64.exe || curl -Lo npp.8.6.7.Installer.x64.exe https://github.com/notepad-plus-plus/notepad-plus-plus/releases/download/v8.6.7/npp.8.6.7.Installer.x64.exe

# https://7-zip.org/
test -f 7z2405-x64.exe || curl -Lo 7z2405-x64.exe https://7-zip.org/a/7z2405-x64.exe


test -f Git-2.45.1-64-bit.exe ||  curl -Lo Git-2.45.1-64-bit.exe https://github.com/git-for-windows/git/releases/download/v2.45.1.windows.1/Git-2.45.1-64-bit.exe

# https://curl.se/windows/

test -f curl-8.8.0_1-win64-mingw.zip ||  curl -Lo curl-8.8.0_1-win64-mingw.zip https://curl.se/windows/dl-8.8.0_1/curl-8.8.0_1-win64-mingw.zip
test -d curl-8.8.0_1-win64-mingw && rm -rf curl-8.8.0_1-win64-mingw
unzip curl-8.8.0_1-win64-mingw.zip

# https://libarchive.org/
test -f libarchive-v3.7.4-amd64.zip ||  curl -Lo libarchive-v3.7.4-amd64.zip https://libarchive.org/downloads/libarchive-v3.7.4-amd64.zip
unzip libarchive-v3.7.4-amd64.zip


```

```bash

# https://learn.microsoft.com/en-us/vcpkg/examples/installing-and-using-packages
# test -d vcpkg || git clone -b master --depth=1 https://github.com/microsoft/vcpkg

# test -f Microsoft.DesktopAppInstaller_8wekyb3d8bbwe.msixbundle ||  curl -Lo Microsoft.DesktopAppInstaller_8wekyb3d8bbwe.msixbundle https://github.com/microsoft/winget-cli/releases/download/v1.7.11261/Microsoft.DesktopAppInstaller_8wekyb3d8bbwe.msixbundle
# rem powershell "add-appxpackage .\Microsoft.DesktopAppInstaller_8wekyb3d8bbwe.msixbundle"
# rem winget install nasm -i


# winget install nasm -i
# https://repo.or.cz/w/nasm.git
# https://www.nasm.us/pub/nasm/releasebuilds/2.16.03/
# https://github.com/netwide-assembler/nasm/blob/master/INSTALL
# https://github.com/netwide-assembler/nasm.git
# test -d nasm || git clone --depth=1 https://github.com/netwide-assembler/nasm.git
# test -f  nasm-2.16.03-win64.zip || curl -Lo nasm-2.16.03-win64.zip https://www.nasm.us/pub/nasm/releasebuilds/2.16.03/win64/nasm-2.16.03-win64.zip
# https://github.com/jingjingxyk/swoole-cli/releases/tag/t-v0.0.3
test -f  nasm-2.16.03-win64.zip || curl -Lo nasm-2.16.03-win64.zip https://github.com/jingjingxyk/swoole-cli/releases/download/t-v0.0.3/nasm-2.16.03-win64.zip
test -d  nasm && rm -rf  nasm
unzip nasm-2.16.03-win64.zip
mv  nasm-2.16.03 nasm
ls -lh nasm



# https://github.com/StrawberryPerl/Perl-Dist-Strawberry/releases/
test -f strawberry-perl-5.38.2.2-64bit.msi ||  curl -Lo strawberry-perl-5.38.2.2-64bit.msi https://github.com/StrawberryPerl/Perl-Dist-Strawberry/releases/download/SP_53822_64bit/strawberry-perl-5.38.2.2-64bit.msi


```

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


dir "%ProgramFiles(x86)%\Microsoft Visual Studio\Installer\vswhere.exe"

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
1. [用于检测和管理 Visual Studio 实例的工具](https://learn.microsoft.com/zh-cn/visualstudio/install/tools-for-managing-visual-studio-instances?view=vs-2022)

```text

set "OLD_TEXT=^"/LD /MD^""
set "NEW_TEXT=^"/MT^""

for /f "delims=" %%i in ('type "%X_MAKEFILE%"') do (
    set "line=%%i"
    set "line=!line:%OLD_TEXT%=%NEW_TEXT%!"
    :: echo !line!>>"%X_MAKEFILE%"
)

```

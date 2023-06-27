# 构建window  PHP 工具 和 参考

[ download windows PHP ](https://windows.php.net/download#php-8.2)

[windows build php 步骤](https://wiki.php.net/internals/windows/stepbystepbuild)

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

## 使用命令行快速安装 VisualStudio 组件

```shell

VisualStudioSetup.exe --add Microsoft.VisualStudio.Workload.NativeDesktop --add Microsoft.VisualStudio.Component.VC.ATLMFC --includeRecommended

```

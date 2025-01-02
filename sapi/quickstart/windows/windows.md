# 构建window  PHP 工具 和 参考

[download windows PHP ](https://windows.php.net/download#php-8.2)

[windows build php 步骤](https://wiki.php.net/internals/windows/stepbystepbuild)

## windows 环境下 git 配置

```shell
git config --global core.autocrlf false
git config --global core.eol lf
git config --global core.ignorecase false
git config core.ignorecase false # 设置 Git 在 Windows 上也区分大小写
```

[Latest VC++](https://learn.microsoft.com/en-AU/cpp/windows/latest-supported-vc-redist)
[7zip](https://7-zip.org/)
[visualstudio](https://visualstudio.microsoft.com/zh-hans/downloads/)
[windows-sdk](https://developer.microsoft.com/en-us/windows/downloads/windows-sdk/)

## windows 软连接例子

```bash

mklink composer composer.phar

```

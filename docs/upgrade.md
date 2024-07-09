## 升级 PHP 版本 步骤

> 1、修改 sapi/PHP-VERSION.conf 文件里版本号即可
> 2、执行同步源码脚本， 拉取 PHP 官方源码 到本项目

```shell

# 测试同步源码
php sync-source-code.php

# 正式同步源码
php sync-source-code.php --action run


```

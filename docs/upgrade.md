## 升级 PHP 版本 步骤

> 1、修改 sapi/PHP-VERSION.conf 文件里版本号即可
> 2、执行同步源码脚本， 拉取 PHP 官方源码 到本项目

```shell

# 测试同步源码
php sync-source-code.php

# 正式同步源码
php sync-source-code.php --action run


./bin/runtime/php -c ./bin/runtime/php.ini  sync-source-code.php
./bin/runtime/php -c ./bin/runtime/php.ini  sync-source-code.php --action run

```

## 目录说明

    pool :持久化目录，存放扩展、 PHP 、依赖库等文件，此目录下的文件不会被主动删除
    var :运行时目录，临时存在一些文件，在完成配置或构建后将被主动清空删除

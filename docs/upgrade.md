# 升级 PHP 版本 步骤

> 1、修改 sapi/PHP-VERSION.conf 文件里版本号即可

> 2、修改 sync-source-code.php 文件里 PHP 源码包的 sha256sum 配置

> 3、执行同步源码脚本， 拉取 PHP 官方源码 到本项目

> 4、sapi/cli/ 代码的升级，需要手动确认

```shell

# 测试同步源码
php sync-source-code.php

# 正式同步源码
php sync-source-code.php --action run


./bin/runtime/php -c ./bin/runtime/php.ini  sync-source-code.php
./bin/runtime/php -c ./bin/runtime/php.ini  sync-source-code.php --action run

```

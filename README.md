# swoole-cli

## 生成构建脚本

```shell
php prepare.php
php prepare.php +inotify +mongodb
```

* 脚本会自动下载相关的`C/C++`库以及`PECL`扩展
* 可使用`+{ext}`或者`-{ext}`增减扩展

## 进入 Docker Bash

```shell
./make.sh docker-bash
```

> 需要将 `swoole-cli` 的目录映射到容器的 `/work` 目录

## 编译配置

```shell
./make.sh config
```

## 构建

```shell
./make.sh build
```

> 编译成功后会生成`bin/swoole-cli`

## 打包

```shell
./make.sh archive
```

## 授权协议

* `swoole-cli`使用了多个其他开源项目，请认真阅读`LICENSE`文件中版权协议，遵守对应开源项目的`LICENSE`
* `swoole-cli`本身的软件源代码、文档等内容以`Apache 2.0 LICENSE`+`SWOOLE-CLI LICENSE`作为双重授权协议，用户需要同时遵守`Apache 2.0 LICENSE`和`SWOOLE-CLI LICENSE`所规定的条款

## SWOOLE-CLI LICENSE

* 对`swoole-cli`代码进行使用、修改、发布的新项目必须含有`SWOOLE-CLI LICENSE`的全部内容
* 使用`swoole-cli`代码重新发布为新项目或者产品时，项目或产品名称不得包含`swoole`单词

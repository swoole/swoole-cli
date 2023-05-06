#

## 当环境中没有PHP 解释器时  可以快速安装 PHP 解释器
```bash

# alpine

bash bash sapi/quickstart/linux/extra/alpine-php-init.sh

## debian

bash bash sapi/quickstart/linux/extra/debian-php-init.sh

```

```bash
   wget -O composer.phar https://mirrors.aliyun.com/composer/composer.phar

```

## c c++编译器

> alpine 使用gcc 默认链接到musl-gcc 不需要把gcc指定为 musl-gcc
> debian 使用gcc 需要指定编译器 musl-gcc

- 组合一 clang clang++
- 组合二 musl-gcc g++


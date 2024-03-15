#

## 当环境中没有 PHP 解释器时  可以快速安装 PHP 解释器

```bash

# alpine

sh  sapi/quickstart/linux/extra/alpine-php-init.sh

## debian

bash sapi/quickstart/linux/extra/debian-php-init.sh

```

## download composer

```bash
curl -Lo  /usr/local/bin/composer.phar https://getcomposer.org/download/latest-stable/composer.phar

ln -sf /usr/local/bin/composer.phar /usr/local/bin/composer
chmod a+x /usr/local/bin/composer

```

## c c++编译器 组合

- 组合一 clang clang++
- 组合二 gcc g++


# 常见问题

## 快速安装 `PHP` 解释器

### Alpine

```bash
sh sapi/quickstart/linux/extra/alpine-php-init.sh
```

### Debian/Ubuntu

```bash
bash sapi/quickstart/linux/extra/debian-php-init.sh
```

## 安装 `Composer`

```bash

curl -Lo  /usr/local/bin/composer.phar https://getcomposer.org/download/latest-stable/composer.phar

ln -sf /usr/local/bin/composer.phar /usr/local/bin/composer
chmod a+x /usr/local/bin/composer

```

## `C/C++` 编译器组合

- `clang` + `clang++`
- `gcc` + `g++`


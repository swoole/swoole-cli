# 步骤
0. 清理 `brew` 安装的软件
1. 执行 `php prepare.php`
2. 编译所有依赖的库 `./make.sh all-library`
3. 配置 `./make.sh config`
4. 构建 `./make.sh build`


## 清理
使用 `brew` 安装的库可能会干扰 `swoole-cli` 的编译，必须要在构建之前将关联的软件进行卸载。在构建完成后再重新安装。

```shell
brew uninstall --ignore-dependencies oniguruma
brew uninstall --ignore-dependencies brotli
brew uninstall --ignore-dependencies freetype
```

# 问题

## 缺少 bison
下载源代码，自行编译安装

## 缺少`libtool`
```shell
ln -s /usr/local/bin/glibtoolize /usr/local/bin/libtoolize
```


## curl configure 检测不通过
修改 `ext/curl/config.m4` ，去掉 `HAVE_CURL` 检测

## `icu/oniguruma` 找不到

错误信息：
```
checking for icu-uc >= 50.1 icu-io icu-i18n... no
configure: error: Package requirements (icu-uc >= 50.1 icu-io icu-i18n) were not met:

No package 'icu-uc' found
No package 'icu-io' found
No package 'icu-i18n' found
```

### 1. 需要手工执行 `export PKG_CONFIG_PATH` 设置路径(复制 `make.sh` 中的指令)
### 2. 设置 `ICU` 相关环境变量

```shell
export ICU_CFLAGS=$(pkg-config --cflags icu-uc)
export ICU_LIBS=$(pkg-config --libs icu-uc)
export ONIG_CFLAGS=$(pkg-config --cflags oniguruma)
export ONIG_LIBS=$(pkg-config --libs oniguruma)
export LIBZIP_CFLAGS=$(pkg-config --cflags libzip)
export LIBZIP_LIBS=$(pkg-config --libs libzip)
export LIBSODIUM_CFLAGS=$(pkg-config --cflags libsodium)
export LIBSODIUM_LIBS=$(pkg-config --libs libsodium)
```


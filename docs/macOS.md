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
brew uninstall --ignore-dependencies zstd
```

# 问题

## 缺少 bison

下载源代码，自行编译安装

## 缺少`libtool`

可使用 `which glibtool` 找到所在路径，使用 `ln -s` 创建软连接

```shell
ln -s /usr/local/bin/glibtool /usr/local/bin/libtool
ln -s /usr/local/bin/glibtoolize /usr/local/bin/libtoolize
```

若使用 `brew` 安装，可能是在 `/opt/homebrew/bin/glibtool` 位置

```shell
ln -s /opt/homebrew/bin/glibtool /opt/homebrew/bin/libtool
ln -s /opt/homebrew/bin/glibtoolize /opt/homebrew/bin/libtoolize
```

## 缺少`gettext coreutils re2c`

```shell
 brew install gettext coreutils re2c
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

## Mac安装应用“提示文件已损坏”或“来自身份不明开发者”解决方法

> 解压以后执行如下命令：

```bash
  sudo xattr -d com.apple.quarantine  ./swoole-cli 
```
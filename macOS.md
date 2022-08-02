# 步骤
0. 清理 `brew` 安装的软件
1. 编译所有依赖的库
2. 配置
3. 构建


## 清理
使用 `brew` 安装的库可能会干扰 `swoole-cli` 的编译，必须要在构建之前将关联的软件进行卸载。在构建完成后再重新安装。

```shell
brew uninstall --ignore-dependencies oniguruma
brew uninstall --ignore-dependencies brotli
brew uninstall --ignore-dependencies freetype
```

# 问题


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
```

## 编译错误
```
In file included from /Users/hantianfeng/workspace/cli-swoole/Zend/zend_config.h:1:
/Users/hantianfeng/workspace/cli-swoole/include/../main/php_config.h:2:1: error: expected external declaration
-ne #ifndef __PHP_CONFIG_H
^
/Users/hantianfeng/workspace/cli-swoole/include/../main/php_config.h:2:2: error: unknown type name 'ne'
-ne #ifndef __PHP_CONFIG_H
 ^
/Users/hantianfeng/workspace/cli-swoole/include/../main/php_config.h:2:5: error: expected identifier or '('
-ne #ifndef __PHP_CONFIG_H
```

需要修改 `main/php_config.h` ，去掉 `-ne` 多余的字符

## 连接错误
直接 `build` 会失败，需要将脚本复制到单独的一个 `build.sh` 中，然后手工修改，加入相关参数。

> 不清楚为什么丢弃了很多编译参数

在 `-all-static -fno-ident` 之后添加： 

```
-L/Users/hantianfeng/workspace/opt/usr/lib -undefined dynamic_lookup -lwebp -licudata -licui18n -licuio
```
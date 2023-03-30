# 打包二进制可执行文件

实现打包 PHP 文件进二进制可执行文件，整项目可以打包为 phar 后再打包进可执行文件，支持 phar 的压缩。
> 说明: sfx 意思是 自执行 

## 测试

**打包 test.php 并运行：**

```shell
./bin/swoole-cli ./bin/pack-sfx.php ./sapi/samples/sfx/test.php ./bin/swoole-cli-test && \
./bin/swoole-cli-test --self
```

**构建 phar：**

```shell
./bin/swoole-cli -dphar.readonly=0 ./sapi/samples/sfx/build-phar.php
```

**打包无压缩的 phar 并运行：**

```shell
./bin/swoole-cli bin/pack-sfx.php ./sapi/samples/sfx/test-none.phar ./bin/swoole-cli-test-phar && \
./bin/swoole-cli-test-phar --self
```

**打包 gz 压缩的 phar 并运行：**

```shell
./bin/swoole-cli bin/pack-sfx.php ./sapi/samples/sfx/test-gz.phar ./bin/swoole-cli-test-phar-gz && \
./bin/swoole-cli-test-phar-gz --self
```

**打包 bz2 压缩的 phar 并运行：**

```shell
./bin/swoole-cli bin/pack-sfx.php ./sapi/samples/sfx/test-bz2.phar ./bin/swoole-cli-test-phar-bz2 && \
./bin/swoole-cli-test-phar-bz2 --self
```

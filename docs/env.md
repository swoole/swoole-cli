环境变量
----
示例：
```shell
SWOOLE_CLI_WITH_BROTLI=yes SWOOLE_CLI_EXT_INCLUDE="./conf.d" php prepare.php +mimalloc -mongodb
```

SKIP_LIBRARY_DOWNLOAD
----
跳过下载依赖库

SWOOLE_CLI_WITH_BROTLI
----
是否开启`brotli`压缩


SWOOLE_CLI_EXT_INCLUDE
----
指定扩展配置的目录，参考`conf.d`

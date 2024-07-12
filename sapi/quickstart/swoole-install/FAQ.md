## 配置扩展加载优先级
    每个扩展配置一个配置文件
    配置文件名称以数字开头 , .ini结尾

## php.ini 配置文件 curl、socks5、mysqld 扩展应该在 swoole 扩展之前加载
    https://github.com/swoole/swoole-src/issues/4085


```bash

# 查看配置所在目录
php --ini | grep  ".ini files"

# 配置 90-swoole.ini
PHP_INI_SCAN_DIR=$(php --ini | grep  "Scan for additional .ini files in:" | awk -F 'in:' '{ print $2 }' | xargs)

if [ ${OS} == 'Linux' ] && [ -n "${PHP_INI_SCAN_DIR}" ] && [ -d "${PHP_INI_SCAN_DIR}" ]; then

  tee  ${PHP_INI_SCAN_DIR}/90-swoole.ini << EOF
extension=swoole.so
swoole.use_shortname=Off
EOF

fi

php --ri swoole

```


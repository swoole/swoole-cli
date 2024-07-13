## 查看扩展加载顺序

```bash

ls -lh `php --ini | grep  "Scan for additional .ini files in:" | awk -F 'in:' '{ print $2 }' | xargs`

```

## php.ini 配置文件 curl、sockets、mysqld 扩展应该在 swoole 扩展之前加载
    https://github.com/swoole/swoole-src/issues/4085

    编译php 时 扫描目录配置 --with-config-file-scan-dir


```bash

# 查看配置所在目录
php --ini | grep  ".ini files"

# 配置 90-swoole.ini
PHP_INI_SCAN_DIR=$(php --ini | grep  "Scan for additional .ini files in:" | awk -F 'in:' '{ print $2 }' | xargs)

if [ -n "${PHP_INI_SCAN_DIR}" ] && [ -d "${PHP_INI_SCAN_DIR}" ]; then
  SUDO=''
  if [ ! -w "${PHP_INI_SCAN_DIR}" ] ; then
    SUDO='sudo'
  fi
  ${SUDO} tee  ${PHP_INI_SCAN_DIR}/90-swoole.ini << EOF
extension=swoole.so
swoole.use_shortname=Off
EOF

fi

php --ri swoole

```


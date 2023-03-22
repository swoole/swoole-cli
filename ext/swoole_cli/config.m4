AC_DEFINE(HAVE_SWOOLE_CLI, 1, [Whether you have swoole_cli])
PHP_NEW_EXTENSION(swoole_cli, swoole_cli.c util.c sfx.c hook_cli.c hook_phar.c, $ext_shared)

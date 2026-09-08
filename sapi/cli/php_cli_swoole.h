#ifndef PHP_CLI_SWOOLE_H
#define PHP_CLI_SWOOLE_H

#include "php.h"
#include "ext/swoole/include/swoole_version.h"
#include "sfx/hook_cli.h"

extern void swoole_cli_self_update(void);
extern int fpm_main(int argc, char *argv[]);

void show_swoole_version(void);

#endif /* PHP_CLI_SWOOLE_H */

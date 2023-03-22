#ifndef PHP_SWOOLE_CLI_HOOK_CLI_H
#define PHP_SWOOLE_CLI_HOOK_CLI_H

#include "php.h"

int swoole_cli_seek_file_self_begin(zend_file_handle *file_handle, char *script_file);

#endif /* PHP_SWOOLE_CLI_HOOK_CLI_H */

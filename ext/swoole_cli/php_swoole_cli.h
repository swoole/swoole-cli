#ifndef PHP_SWOOLE_CLI_H
#define PHP_SWOOLE_CLI_H

extern zend_module_entry swoole_cli_module_entry;
#define phpext_swoole_cli_ptr &swoole_cli_module_entry

#include "php_version.h"
#define PHP_SWOOLE_CLI_VERSION PHP_VERSION

#endif /* PHP_SWOOLE_CLI_H */

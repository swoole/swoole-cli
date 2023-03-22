#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_swoole_cli.h"
#include "swoole_cli_arginfo.h"

#ifdef HAVE_SWOOLE_CLI

/* PHP Includes */
#include "ext/standard/info.h"

static PHP_MINIT_FUNCTION(swoole_cli);
static PHP_MSHUTDOWN_FUNCTION(swoole_cli);
static PHP_MINFO_FUNCTION(swoole_cli);

static const zend_module_dep swoole_cli_deps[] = {
	ZEND_MOD_OPTIONAL("phar")
	ZEND_MOD_END
};

zend_module_entry swoole_cli_module_entry = {
	STANDARD_MODULE_HEADER_EX, NULL,
	swoole_cli_deps,
	"swoole_cli",
	NULL,
	PHP_MINIT(swoole_cli),
	PHP_MSHUTDOWN(swoole_cli),
	NULL,
	NULL,
	PHP_MINFO(swoole_cli),
	PHP_SWOOLE_CLI_VERSION,
	STANDARD_MODULE_PROPERTIES
};

#ifdef COMPILE_DL_SWOOLE_CLI
ZEND_GET_MODULE(swoole_cli)
#endif

static PHP_MINIT_FUNCTION(swoole_cli)
{
	return SUCCESS;
}

static PHP_MSHUTDOWN_FUNCTION(swoole_cli)
{
	return SUCCESS;
}

static PHP_MINFO_FUNCTION(swoole_cli)
{
	php_info_print_table_start();
	php_info_print_table_row(2, "swoole-cli", "Enabled");
	php_info_print_table_end();
}

#endif

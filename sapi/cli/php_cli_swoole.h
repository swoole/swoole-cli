#ifndef PHP_CLI_SWOOLE_H
#define PHP_CLI_SWOOLE_H

#include "php.h"
#include "ext/swoole/include/swoole_version.h"
#include "sfx/hook_cli.h"

extern void swoole_cli_self_update(void);
extern int fpm_main(int argc, char *argv[]);

static inline void show_swoole_version(void) {
    php_printf("Swoole %s (%s) (built: %s %s) (%s)\n",
        SWOOLE_VERSION, cli_sapi_module.name, __DATE__, __TIME__,
#ifdef ZTS
        "ZTS"
#else
        "NTS"
#endif
#ifdef PHP_BUILD_COMPILER
        " " PHP_BUILD_COMPILER
#endif
#ifdef PHP_BUILD_ARCH
        " " PHP_BUILD_ARCH
#endif
#if ZEND_DEBUG
        " DEBUG"
#endif
#ifdef HAVE_GCOV
        " GCOV"
#endif
    );
}

#endif /* PHP_CLI_SWOOLE_H */

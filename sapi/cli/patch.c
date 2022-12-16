#include "php.h"
#include "library.h"

#ifdef __CYGWIN__
#include "ext/zip/php_zip.h"
int zip_encryption_method_supported(zip_int16_t method, int encrypt) {
    return 1;
}
#endif

zend_module_entry opcache_module_entry = {
    STANDARD_MODULE_HEADER_EX,
    NULL,
    NULL,
    "opcache",
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    PHP_VERSION,
    STANDARD_MODULE_PROPERTIES
};

void swoole_cli_self_update(void) {
    php_swoole_cli_load_library();
    zend_eval_string_ex("swoole_cli_self_update();", NULL, "self update", 1);
}


#include "php.h"


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

#if defined(PHP_WIN32)
int fpm_main(int argc, char *argv[])
{
	(void)argc;
	(void)argv;
	fprintf(stderr,"FPM is not supported on Windows . Use the standard CLI SAPI instead.\n");
	return 1;
}
#endif

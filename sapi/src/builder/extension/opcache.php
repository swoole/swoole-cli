<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('opcache'))
            ->withHomePage('https://www.php.net/opcache')
            ->withOptions('--enable-opcache')
    );

    // 扩展钩子 写法
    $p->withBeforeConfigureScript('opcache', function (Preprocessor $p) {
        $workDir = $p->getPhpSrcDir();
        $cmd = <<<EOF
        cd {$workDir}/ ;

EOF;

        $cmd .= <<<'EOF'

        cat > ext/opcache/php_opcache.h <<PHP_OPCACHE_H_EOF
#include "php.h"

extern zend_module_entry opcache_module_entry;
#define phpext_opcache_ptr  &opcache_module_entry

PHP_OPCACHE_H_EOF

        sed -i.backup 's/ext_shared=yes/ext_shared=no/g' ext/opcache/config.m4
        sed -i.backup 's/shared,,/\$ext_shared,,/g' ext/opcache/config.m4

        sed -i.backup 's/\/\* start Zend extensions \*\//\/\* start Zend extensions \*\/\n#ifdef PHP_ENABLE_OPCACHE\n\textern zend_extension zend_extension_entry;\n\tzend_register_extension(\&zend_extension_entry, NULL);\n#endif/g' main/main.c

        cat >> sapi/cli/php_cli.c <<PHP_CLI_OPCACHE_EOF

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

PHP_CLI_OPCACHE_EOF

        cat >> sapi/fpm/fpm/fpm.c <<PHP_FPM_OPCACHE_EOF

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

PHP_FPM_OPCACHE_EOF

        test -f main/main.c.backup && rm -f main/main.c.backup
        test -f ext/opcache/config.m4.backup && rm -f ext/opcache/config.m4.backup


EOF;

        if (BUILD_CUSTOM_PHP_VERSION_ID >= 8050) {
            $cmd = <<<EOF
        cd {$workDir}/ ;
        sed -i.backup 's/ext_shared=yes/ext_shared=no/g' ext/opcache/config.m4
        sed -i.backup 's/shared,,/\$ext_shared,,/g' ext/opcache/config.m4
EOF;

        }

        return $cmd;
    });
};

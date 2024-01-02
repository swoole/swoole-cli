<?php

declare(strict_types=1);


$php_version_tag = trim(file_get_contents(__DIR__ . '/sapi/PHP-VERSION.conf'));
$project_dir = __DIR__;
$file = __DIR__ . "/pool/lib/php-{$php_version_tag}.tar.gz";
$cmd = "curl -L https://github.com/php/php-src/archive/refs/tags/php-{$php_version_tag}.tar.gz -o $file";
echo $cmd . PHP_EOL;
if (!file_exists($file)) {
    `{$cmd}`;
}
$php_source_folder = __DIR__ . "/var/php-{$php_version_tag}";

# tar -zxvf 文件名.tar.gz --strip-components=1 -C 指定解压目录

$cmd = <<<EOF
    test -d {$php_source_folder} && rm -rf {$php_source_folder}
    mkdir -p {$php_source_folder}
    tar -zxvf {$file} --strip-components=1 -C  {$php_source_folder}

    SRC={$php_source_folder}

    cd {$project_dir}
EOF;

$cmd .= <<<'EOF'

    echo "sync"
    # ZendVM
    cp -r $SRC/Zend ./

    # Extension
    cp -r $SRC/ext/bcmath/ ./ext
    cp -r $SRC/ext/bz2/ ./ext
    cp -r $SRC/ext/calendar/ ./ext
    cp -r $SRC/ext/ctype/ ./ext
    cp -r $SRC/ext/curl/ ./ext
    cp -r $SRC/ext/date/ ./ext
    cp -r $SRC/ext/dom/ ./ext
    cp -r $SRC/ext/exif/ ./ext
    cp -r $SRC/ext/fileinfo/ ./ext
    cp -r $SRC/ext/filter/ ./ext
    cp -r $SRC/ext/gd/ ./ext
    cp -r $SRC/ext/gettext/ ./ext
    cp -r $SRC/ext/gmp/ ./ext
    cp -r $SRC/ext/hash/ ./ext
    cp -r $SRC/ext/iconv/ ./ext
    cp -r $SRC/ext/intl/ ./ext
    cp -r $SRC/ext/json/ ./ext
    cp -r $SRC/ext/libxml/ ./ext
    cp -r $SRC/ext/mbstring/ ./ext
    cp -r $SRC/ext/mysqli/ ./ext
    cp -r $SRC/ext/mysqlnd/ ./ext
    cp -r $SRC/ext/opcache/ ./ext
    sed -i 's/ext_shared=yes/ext_shared=no/g' ext/opcache/config.m4 && sed -i 's/shared,,/$ext_shared,,/g' ext/opcache/config.m4
    sed -i 's/-DZEND_ENABLE_STATIC_TSRMLS_CACHE=1/-DZEND_ENABLE_STATIC_TSRMLS_CACHE=1 -DPHP_ENABLE_OPCACHE/g' ext/opcache/config.m4
    echo -e '#include "php.h"\n\nextern zend_module_entry opcache_module_entry;\n#define phpext_opcache_ptr  &opcache_module_entry\n' > ext/opcache/php_opcache.h
    cp -r $SRC/ext/openssl/ ./ext
    cp -r $SRC/ext/pcntl/ ./ext
    cp -r $SRC/ext/pcre/ ./ext
    cp -r $SRC/ext/pdo/ ./ext
    cp -r $SRC/ext/pdo_mysql/ ./ext
    cp -r $SRC/ext/pdo_sqlite/ ./ext
    cp -r $SRC/ext/phar/ ./ext
    echo -e '\n#include "sapi/cli/sfx/hook_stream.h"' >> ext/phar/phar_internal.h
    cp -r $SRC/ext/posix/ ./ext
    cp -r $SRC/ext/readline/ ./ext
    cp -r $SRC/ext/reflection/ ./ext
    cp -r $SRC/ext/session/ ./ext
    cp -r $SRC/ext/simplexml/ ./ext
    cp -r $SRC/ext/soap/ ./ext
    cp -r $SRC/ext/sockets/ ./ext
    cp -r $SRC/ext/sodium/ ./ext
    cp -r $SRC/ext/spl/ ./ext
    cp -r $SRC/ext/sqlite3/ ./ext
    cp -r $SRC/ext/standard/ ./ext
    cp -r $SRC/ext/sysvshm/ ./ext
    cp -r $SRC/ext/tokenizer/ ./ext
    cp -r $SRC/ext/xml/ ./ext
    cp -r $SRC/ext/xmlreader/ ./ext
    cp -r $SRC/ext/xmlwriter/ ./ext
    cp -r $SRC/ext/xsl/ ./ext
    cp -r $SRC/ext/zip/ ./ext
    cp -r $SRC/ext/zlib/ ./ext

    # main
    cp -r $SRC/main ./
    sed -i 's/\/\* start Zend extensions \*\//\/\* start Zend extensions \*\/\n#ifdef PHP_ENABLE_OPCACHE\n\textern zend_extension zend_extension_entry;\n\tzend_register_extension(\&zend_extension_entry, NULL);\n#endif/g' main/main.c

    # build
    cp -r $SRC/build ./
    # TSRM
    cp -r ./TSRM/TSRM.h main/TSRM.h
    cp -r $SRC/configure.ac ./

    # fpm
    cp -r $SRC/sapi/fpm/fpm ./sapi/cli
    exit 0

EOF;

echo $cmd . PHP_EOL;
`$cmd`;

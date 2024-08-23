<?php

declare(strict_types=1);

$project_dir = __DIR__;
require_once __DIR__ . '/sapi/scripts/DownloadPHPSourceCode.php';
$php_source_folder = PHP_SRC_DIR;
$sync_dest_dir = $project_dir . '/var/sync-source-code-tmp';

$scanned_directory_source = array_diff(scandir($php_source_folder . '/ext/'), array('..', '.'));
$scanned_directory_destination = array_diff(scandir($project_dir . '/ext/'), array('..', '.'));


$SYNC_SOURCE_CODE_SHELL = 'set -x';

# 默认同步代码 到测试验证目录:  php sync-source-code.php
# 正式同步代码 请执行命令:     php sync-source-code.php --action run


$action = 'dry_run';
$longopts = array(
    "action:"
);
$options = getopt('', $longopts);

if (!empty($options['action']) && $options['action'] == 'run') {
    //正式同步
    $action = 'run';
    $sync_dest_dir = $project_dir;
} else {
    //测试同步
    # 准备工作 测试目录


    $directories = array_intersect($scanned_directory_source, $scanned_directory_destination);

    `test -d {$sync_dest_dir} && rm -rf {$sync_dest_dir}`;
    `mkdir -p {$sync_dest_dir}`;

    foreach ($directories as $directory) {
        # echo "mkdir -p {$sync_dest_dir}/ext/{$directory}" . PHP_EOL;
        `mkdir -p {$sync_dest_dir}/ext/{$directory}`;

    }

    $SYNC_SOURCE_CODE_SHELL .= PHP_EOL . <<<EOF
    cd {$sync_dest_dir}
    mkdir -p ./sapi/cli
    mkdir -p ./sapi/cli/fpm/
    mkdir -p ./TSRM/
    mkdir -p ./Zend/
    mkdir -p ./build/
    mkdir -p ./main/
    mkdir -p ./scripts/

EOF;

}

#  执行代码同步之前准备
$SYNC_SOURCE_CODE_SHELL .= PHP_EOL . <<<EOF
    SRC={$php_source_folder}
    cd {$sync_dest_dir}
EOF;

# 准备 同步代码脚本
$SYNC_SOURCE_CODE_SHELL .= PHP_EOL . <<<'EOF'

    echo "sync"
    # ZendVM
    cp -rf $SRC/Zend/ ./Zend

    # Extension
    cp -rf $SRC/ext/bcmath/ ./ext/bcmath
    cp -rf $SRC/ext/bz2/ ./ext/bz2
    cp -rf $SRC/ext/calendar/ ./ext/calendar
    cp -rf $SRC/ext/ctype/ ./ext/ctype
    cp -rf $SRC/ext/curl/ ./ext/curl
    cp -rf $SRC/ext/date/ ./ext/date
    cp -rf $SRC/ext/dom/ ./ext/dom
    cp -rf $SRC/ext/exif/ ./ext/exif
    cp -rf $SRC/ext/fileinfo/ ./ext/fileinfo
    cp -rf $SRC/ext/filter/ ./ext/filter
    cp -rf $SRC/ext/gd/ ./ext/gd
    cp -rf $SRC/ext/gettext/ ./ext/gettext
    cp -rf $SRC/ext/gmp/ ./ext/gmp
    cp -rf $SRC/ext/hash/ ./ext/hash
    cp -rf $SRC/ext/iconv/ ./ext/iconv
    cp -rf $SRC/ext/intl/ ./ext/intl
    cp -rf $SRC/ext/json/ ./ext/json
    cp -rf $SRC/ext/libxml/ ./ext/libxml
    cp -rf $SRC/ext/mbstring/ ./ext/mbstring
    cp -rf $SRC/ext/mysqli/ ./ext/mysqli
    cp -rf $SRC/ext/mysqlnd/ ./ext/mysqlnd
    cp -rf $SRC/ext/opcache/ ./ext/opcache

    sed -i.backup 's/ext_shared=yes/ext_shared=no/g' ext/opcache/config.m4
    sed -i.backup 's/shared,,/$ext_shared,,/g' ext/opcache/config.m4
    # echo '#include "php.h"\n\nextern zend_module_entry opcache_module_entry;\n#define phpext_opcache_ptr  &opcache_module_entry\n' > ext/opcache/php_opcache.h
    cat > ext/opcache/php_opcache.h <<PHP_OPCACHE_H_EOF
#include "php.h"

extern zend_module_entry opcache_module_entry;
#define phpext_opcache_ptr  &opcache_module_entry

PHP_OPCACHE_H_EOF

    cp -rf $SRC/ext/openssl/ ./ext/openssl
    cp -rf $SRC/ext/pcntl/ ./ext/pcntl
    cp -rf $SRC/ext/pcre/ ./ext/pcre
    cp -rf $SRC/ext/pdo/ ./ext/pdo
    cp -rf $SRC/ext/pdo_mysql/ ./ext/pdo_mysql

    cp -rf $SRC/ext/phar/ ./ext/phar
    echo '\n#include "sapi/cli/sfx/hook_stream.h"' >> ext/phar/phar_internal.h

    cp -rf $SRC/ext/posix/ ./ext/posix
    cp -rf $SRC/ext/readline/ ./ext/readline
    cp -rf $SRC/ext/reflection/ ./ext/reflection
    cp -rf $SRC/ext/session/ ./ext/session
    cp -rf $SRC/ext/simplexml/ ./ext/simplexml
    cp -rf $SRC/ext/soap/ ./ext/soap
    cp -rf $SRC/ext/sockets/ ./ext/sockets
    cp -rf $SRC/ext/sodium/ ./ext/sodium
    cp -rf $SRC/ext/spl/ ./ext/spl
    cp -rf $SRC/ext/sqlite3/ ./ext/sqlite3
    cp -rf $SRC/ext/standard/ ./ext/standard
    cp -rf $SRC/ext/sysvshm/ ./ext/sysvshm
    cp -rf $SRC/ext/tokenizer/ ./ext/tokenizer
    cp -rf $SRC/ext/xml/ ./ext/xml
    cp -rf $SRC/ext/xmlreader/ ./ext/xmlreader
    cp -rf $SRC/ext/xmlwriter/ ./ext/xmlwriter
    cp -rf $SRC/ext/xsl/ ./ext/xsl
    cp -rf $SRC/ext/zip/ ./ext/zip
    cp -rf $SRC/ext/zlib/ ./ext/zlib

    # main
    cp -rf $SRC/main/ ./main
    sed -i.backup 's/\/\* start Zend extensions \*\//\/\* start Zend extensions \*\/\n#ifdef PHP_ENABLE_OPCACHE\n\textern zend_extension zend_extension_entry;\n\tzend_register_extension(\&zend_extension_entry, NULL);\n#endif/g' main/main.c

    # build
    cp -rf $SRC/build/ ./build

    # TSRM
    cp -rf $SRC/TSRM/ ./TSRM
    cp -rf $SRC/TSRM/TSRM.h main/TSRM.h

    cp -f $SRC/configure.ac ./configure.ac
    cp -f $SRC/buildconf ./buildconf
    cp -f $SRC/run-tests.php ./run-tests.php

    # scripts
    cp -rf $SRC/scripts/ ./scripts

    # 在sed命令中，常见的需要转义的字符有：\、/、$、&、.、*、[、]等
    #                                反斜杠、正斜杠、美元符号、引用符号、点号、星号、方括号等

    test -f main/main.c.backup && rm -f main/main.c.backup
    test -f ext/opcache/config.m4.backup && rm -f ext/opcache/config.m4.backup
    exit 0

    # fpm
    cp -rf $SRC/sapi/fpm/fpm ./sapi/cli/
    sed -i.backup 's/int main(int argc, char \*argv\[\])/int fpm_main(int argc, char \*argv\[\])/g' ./sapi/cli/fpm/fpm_main.c
    sed -i.backup 's/{'-', 0, NULL}/{'P', 0, "fpm"},\n	{'-', 0, NULL}/g' ./sapi/cli/fpm/fpm_main.c


    # exit 0

    # cli
    cp -rf $SRC/sapi/cli/ps_title.c ./sapi/cli
    cp -rf $SRC/sapi/cli/generate_mime_type_map.php ./sapi/cli
    cp -rf $SRC/sapi/cli/php.1.in ./sapi/cli

EOF;

echo PHP_EOL;
# 显示将要执行的同步命令
echo $SYNC_SOURCE_CODE_SHELL;
echo PHP_EOL;
echo PHP_EOL;
# 执行同步
echo "synchronizing  .... ";
echo PHP_EOL;
echo PHP_EOL;
echo `$SYNC_SOURCE_CODE_SHELL`;
echo PHP_EOL;
echo PHP_EOL;
echo "synchronizing  end  ";
echo PHP_EOL;
echo PHP_EOL;
echo "action: " . $action . ' done !' . PHP_EOL;

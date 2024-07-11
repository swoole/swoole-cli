<?php

$poject_dir = realpath(__DIR__ . '/../../');
# $php_version_tag = trim(file_get_contents($poject_dir . '/sapi/PHP-VERSION.conf'));
$php_version_tag = "8.2.13";
define("PHP_SRC_DIR", $poject_dir . "/var/php-{$php_version_tag}");
$php_source_folder = PHP_SRC_DIR;
$php_file = $poject_dir . "/pool/lib/php-{$php_version_tag}.tar.gz";
$download_dir = dirname($php_file);


# 下载 PHP 源码
$DOWNLOAD_PHP_CMD = "curl -L https://github.com/php/php-src/archive/refs/tags/php-{$php_version_tag}.tar.gz -o {$php_file}";
echo $DOWNLOAD_PHP_CMD . PHP_EOL;
if (!file_exists($php_file)) {
    `test -d {$download_dir} || mkdir -p {$download_dir}`;
    `{$DOWNLOAD_PHP_CMD}`;
}

# 解压 PHP 源码
# tar -zxvf 文件名.tar.gz --strip-components=1 -C 指定解压目录
$UNTAR_PHP_SOURCE_CMD = <<<EOF
    set -x
    # test -d {$php_source_folder} && rm -rf {$php_source_folder}
    mkdir -p {$php_source_folder}
    test -f {$php_source_folder}/configure.ac || tar -zxf {$php_file} --strip-components=1 -C  {$php_source_folder}
EOF;

`{$UNTAR_PHP_SOURCE_CMD}`;

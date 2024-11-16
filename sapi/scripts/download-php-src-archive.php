<?php

$poject_dir = realpath(__DIR__ . '/../../');

# $php_version_tag = trim(file_get_contents($poject_dir . '/sapi/PHP-VERSION.conf'));
$php_version_tag = '8.2.23';
$php_source_folder = $poject_dir . "/var/php-{$php_version_tag}";
$php_archive_file = $poject_dir . "/pool/php/php-{$php_version_tag}.tar.gz";

$download_dir = dirname($php_archive_file);

# 下载 PHP 源码
$download_cmd = "curl -L https://github.com/php/php-src/archive/refs/tags/php-{$php_version_tag}.tar.gz -o {$php_archive_file}";
echo $download_cmd . PHP_EOL;
if (!file_exists($php_archive_file)) {
    `test -d {$download_dir} || mkdir -p {$download_dir}`;
    `{$download_cmd}`;
}

# 若不存在则解压 PHP 源码包
# tar -zxvf 文件名.tar.gz --strip-components=1 -C 指定解压目录

$extract_tar_cmd = <<<EOF
    set -x
    # test -d {$php_source_folder} && rm -rf {$php_source_folder}
    mkdir -p {$php_source_folder}
    test -f {$php_source_folder}/configure.ac || tar -zxf {$php_archive_file} --strip-components=1 -C  {$php_source_folder}
EOF;

`{$extract_tar_cmd}`;

return $php_source_folder;

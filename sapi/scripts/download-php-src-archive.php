<?php

$project_dir = realpath(__DIR__ . '/../../');
$php_version_tag = trim(file_get_contents($project_dir . '/sapi/PHP-VERSION.conf'));
# $php_version_tag = '8.2.29';
$php_source_folder = $project_dir . "/var/php-{$php_version_tag}";
$php_archive_file = $project_dir . "/pool/php-tar/php-{$php_version_tag}.tar.gz";
// github.com
$php_archive_file_sha256sum = '22bd132176a2ff5140dd38d30213364ce1119edda4521280d5249bc1f55721e9';
// php.net
// $php_archive_file_sha256sum = '40341f3e03a36d48facdb6cc2ec600ff887a1af9a5e5fee0b40f40b61488afae';

$download_dir = dirname($php_archive_file);
$download_php_counter = 0;

DOWNLOAD_PHP:
# 下载 PHP 源码
$download_cmd = "curl -fSL https://github.com/php/php-src/archive/refs/tags/php-{$php_version_tag}.tar.gz -o {$php_archive_file}";
echo $download_cmd . PHP_EOL;
if (!file_exists($php_archive_file)) {
    `test -d {$download_dir} || mkdir -p {$download_dir}`;
    $download_php_counter++;
    `{$download_cmd}`;
}

$hash = hash_file('sha256', $php_archive_file);
echo "sha256sum: " . $hash . PHP_EOL;
if ($hash !== $php_archive_file_sha256sum) {
    if ($download_php_counter > 3) {
        throw  new \Exception('curl download php archive Exception!', 500);
    }
    echo 'archive sha256sum mismatched , will re-download ' . PHP_EOL;
    unlink($php_archive_file);
    goto    DOWNLOAD_PHP;
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

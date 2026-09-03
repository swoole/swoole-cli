<?php

$project_dir = realpath(__DIR__ . '/../../');
$php_version_tag = trim(file_get_contents($project_dir . '/sapi/PHP-VERSION.conf'));
$php_source_folder = $project_dir . "/var/php-{$php_version_tag}";
$php_archive_file = $project_dir . "/pool/php-tar/php-{$php_version_tag}.tar.gz";
$download_dir = dirname($php_archive_file);

// 从 GitHub 下载 php-src 的 tag 归档。
//
// 注意：GitHub 的 tag 归档是动态打包的，其 sha256 每个版本都不同、且不对外公布，
// 因此这里不校验 sha256（下载走 HTTPS，本身有 TLS 完整性保证）。这也是为什么
// 版本升级后不能写死 sha256 —— 否则每次升级都要重新手算，导致无法自动下载。
$download_cmd = "curl -fSL https://github.com/php/php-src/archive/refs/tags/php-{$php_version_tag}.tar.gz -o {$php_archive_file}";

// 若归档不存在或损坏（文件过小）则下载
$need_download = !file_exists($php_archive_file) || filesize($php_archive_file) < 1024;
if ($need_download) {
    echo "downloading php-src {$php_version_tag}" . PHP_EOL;
    echo $download_cmd . PHP_EOL;
    `test -d {$download_dir} || mkdir -p {$download_dir}`;
    `{$download_cmd}`;
    if (!file_exists($php_archive_file) || filesize($php_archive_file) < 1024) {
        throw new \Exception("download php-src archive failed: {$php_archive_file}", 500);
    }
} else {
    echo "php-src archive cached: {$php_archive_file}" . PHP_EOL;
}

// 若源码目录尚未解压则解压
if (!file_exists("{$php_source_folder}/configure.ac")) {
    echo "unpacking php-src {$php_version_tag}" . PHP_EOL;
    `mkdir -p {$php_source_folder}`;
    `tar -zxf {$php_archive_file} --strip-components=1 -C {$php_source_folder}`;
    if (!file_exists("{$php_source_folder}/configure.ac")) {
        throw new \Exception("unpack php-src archive failed: {$php_source_folder}", 500);
    }
}

return $php_source_folder;

#!/usr/bin/env php
<?php
$shell = "";
switch (PHP_OS) {
    case 'Linux':
        $shell = "./bin/swoole-cli -m | tail -n +2 | head -n -3 ";
        break;
    case 'Darwin':
        $shell = "./bin/swoole-cli -m | tail -n +2 | ghead -n -3 ";
        break;
    case 'WINNT':
    default:
        echo "no support this OS ";
        exit(0);

}
$list_swoole_cli = swoole_string(`$shell`)->trim()->lower()->split(PHP_EOL)
    ->remove('core');

ob_start();
require_once __DIR__ . '/sapi/scripts/DownloadPHPSourceCode.php';
$php_source_folder = PHP_SRC_DIR;
ob_end_clean();

$list_php_src = swoole_string(`ls -1 {$php_source_folder}/ext/`)->trim()->lower()->split(PHP_EOL)
    ->remove('ext_skel.php')
    ->remove('zend_test');

$list_intersect = array_intersect($list_php_src->toArray(), $list_swoole_cli->toArray());

$diff1 = array_diff($list_swoole_cli->toArray(), $list_intersect);
echo "Added(" . count($diff1) . ")\n===============================================================\n";
foreach ($diff1 as $v) {
    echo '+ ' . $v . PHP_EOL;
}
echo PHP_EOL;

$diff2 = array_diff($list_php_src->toArray(), $list_intersect);
echo "Removed(" . count($diff2) . ")\n==============================================================\n";
foreach ($diff2 as $v) {
    echo '- ' . $v . PHP_EOL;
}
echo PHP_EOL;

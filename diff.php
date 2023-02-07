#!/usr/bin/env php
<?php
$list_swoole_cli = swoole_string(`./bin/swoole-cli -m | tail -n +2 | head -n -3`)->trim()->lower()->split(PHP_EOL)
    ->remove('core');

$list_php_src = swoole_string(`ls -1 ~/soft/php-8.1.5/ext`)->trim()->lower()->split(PHP_EOL)
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

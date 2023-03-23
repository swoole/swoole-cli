#!/usr/bin/env php
<?php

if (!defined('SWOOLE_CLI')) {
    echo 'Please use the swoole-cli to run this script.', PHP_EOL;
    exit(1);
}

$argv = $_SERVER['argv'];
if (!isset($argv[2])) {
    echo 'Wrong arguments! Example: ./swoole-cli pack.php your.php target', PHP_EOL;
    exit(1);
}
$phpFile = $argv[1];
$target = $argv[2];

if (!copy(PHP_BINARY, $target)) {
    echo sprintf('Copy file %s to %s failed!', PHP_BINARY, $target), PHP_EOL;
    exit(1);
}

exec($cmd = sprintf('chmod +x %s', $target), $output, $status);
if (0 !== $status) {
    echo sprintf('Exec cmd %s failed!', $cmd), PHP_EOL;
    exit(1);
}

$phpFileSize = filesize($phpFile);
if (false === $phpFileSize) {
    echo sprintf('Get file %s size failed!', $phpFile), PHP_EOL;
    exit(1);
}

exec($cmd = sprintf('cat %s >> %s', $phpFile, $target), $output, $status);
if (0 !== $status) {
    echo sprintf('Exec cmd %s failed!', $cmd), PHP_EOL;
    exit(1);
}

// Write php file size to the end of the target file.
$fp = fopen($target, 'a');
if (false === $fp) {
    echo sprintf('Open file %s failed!', $target), PHP_EOL;
    exit(1);
}
if (false === fwrite($fp, pack('J', $phpFileSize))) {
    echo sprintf('Write file %s failed!', $target), PHP_EOL;
    exit(1);
}
fclose($fp);

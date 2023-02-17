#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';

use SwooleCli\Preprocessor;

$homeDir = getenv('HOME');
$p = new Preprocessor(__DIR__);
$p->parseArguments($argc, $argv);
$p->setPhpSrcDir(getenv('HOME') . '/.phpbrew/build/php-8.1.12');
if ($p->getOsType() == 'macos') {
    $p->setWorkDir(__DIR__);
    $p->setBuildDir(__DIR__ . '/thirdparty');
    define('GLOBAL_PREFIX', $homeDir . '/.swoole-cli');
    $p->setExtraLdflags('-framework CoreFoundation -framework SystemConfiguration -undefined dynamic_lookup');
    $p->addEndCallback(function () use ($p) {
        if (!is_dir(GLOBAL_PREFIX)) {
            mkdir(GLOBAL_PREFIX);
        }
    });
} else {
    define('GLOBAL_PREFIX', '/usr');
}
$p->execute();

#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';

use SwooleCli\Preprocessor;

$homeDir = getenv('HOME');

$p = new Preprocessor(__DIR__);
$p->setPhpSrcDir($homeDir . '/.phpbrew/build/php-8.1.12');
$p->setDockerVersion('1.5');
if ($p->getOsType() == 'macos') {
    $p->setWorkDir(__DIR__);
    $p->setExtraLdflags('-framework CoreFoundation -framework SystemConfiguration -undefined dynamic_lookup -lwebp -licudata -licui18n -licuio');
    $p->addEndCallback(function () use ($p, $homeDir) {
        $libDir = $homeDir . '/.swoole-cli';
        if (!is_dir($libDir)) {
            mkdir($libDir);
        }
        // The lib directory MUST not be in the current directory, otherwise the php make clean script will delete librarys
        file_put_contents(__DIR__ . '/make.sh', str_replace('/usr', $homeDir . '/.swoole-cli', file_get_contents(__DIR__ . '/make.sh')));
    });
}
$p->parseArguments($argc, $argv);
$p->gen();
$p->info();

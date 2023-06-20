#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';

use SwooleCli\Preprocessor;

const BUILD_PHP_VERSION = '8.1.12';

$homeDir = getenv('HOME');
$p = Preprocessor::getInstance();
$p->parseArguments($argc, $argv);

// Sync code from php-src
$p->setPhpSrcDir($homeDir . '/.phpbrew/build/php-' . BUILD_PHP_VERSION);

// Compile directly on the host machine, not in the docker container
if ($p->getInputOption('without-docker') || ($p->getOsType() == 'macos')) {
    $p->setWorkDir(__DIR__);
    $p->setBuildDir(__DIR__ . '/thirdparty');
}

if ($p->getInputOption('with-global-prefix')) {
    $p->setGlobalPrefix($p->getInputOption('with-global-prefix'));
}

if ($p->getInputOption('with-parallel-jobs')) {
    $p->setMaxJob(intval($p->getInputOption('with-parallel-jobs')));
}

if ($p->getOsType() == 'macos') {
    $p->setExtraLdflags('-undefined dynamic_lookup');
    $p->setLinker('ld');
    if (is_file('/usr/local/opt/llvm/bin/ld64.lld')) {
        $p->withBinPath('/usr/local/opt/llvm/bin')->setLinker('ld64.lld');
    }
    $p->setLogicalProcessors('$(sysctl -n hw.ncpu)');
} else {
    $p->setLinker('ld.lld');
    $p->setLogicalProcessors('$(nproc 2> /dev/null)');
}

$p->setExtraCflags('-fno-ident -Os');

// Generate make.sh
$p->execute();

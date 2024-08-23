#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';

use SwooleCli\Preprocessor;

$php_version_tag = trim(file_get_contents(__DIR__ . '/sapi/PHP-VERSION.conf'));
define('BUILD_PHP_VERSION', $php_version_tag);


$homeDir = getenv('HOME');
$p = Preprocessor::getInstance();
$p->parseArguments($argc, $argv);

$buildType = $p->getBuildType();
if ($p->getInputOption('with-build-type')) {
    $buildType = $p->getInputOption('with-build-type');
    $p->setBuildType($buildType);
}

# clean old make.sh
if (($buildType == 'dev') && file_exists(__DIR__ . '/make.sh')) {
    unlink(__DIR__ . '/make.sh');
}

// Sync code from php-src
$p->setPhpSrcDir($p->getWorkDir() . '/var/php-' . BUILD_PHP_VERSION);

// Compile directly on the host machine, not in the docker container
if ($p->getInputOption('without-docker') || ($p->isMacos())) {
    $p->setWorkDir(__DIR__);
    $p->setBuildDir(__DIR__ . '/thirdparty');
}


if ($p->getInputOption('with-global-prefix')) {
    $p->setGlobalPrefix($p->getInputOption('with-global-prefix'));
}

if ($p->getInputOption('with-parallel-jobs')) {
    $p->setMaxJob(intval($p->getInputOption('with-parallel-jobs')));
}

if ($p->isMacos()) {
    $p->setExtraLdflags('-undefined dynamic_lookup');
    if (is_file('/usr/local/opt/llvm/bin/ld64.lld')) {
        $p->withBinPath('/usr/local/opt/llvm/bin')
            ->withBinPath('/usr/local/opt/flex/bin')
            ->withBinPath('/usr/local/opt/bison/bin')
            ->withBinPath('/usr/local/opt/libtool/bin')
            ->withBinPath('/usr/local/opt/m4/bin')
            ->withBinPath('/usr/local/opt/automake/bin/')
            ->withBinPath('/usr/local/opt/autoconf/bin/')
            ->setLinker('ld64.lld');
    } elseif (is_file('/opt/homebrew/opt/llvm/bin/ld64.lld')) {
        $p->withBinPath('/opt/homebrew/opt/llvm/bin/')
            ->withBinPath('/opt/homebrew/opt/flex/bin')
            ->withBinPath('/opt/homebrew/opt/bison/bin')
            ->withBinPath('/opt/homebrew/opt/libtool/bin')
            ->withBinPath('/opt/homebrew/opt/m4/bin')
            ->withBinPath('/opt/homebrew/opt/automake/bin/')
            ->withBinPath('/opt/homebrew/opt/autoconf/bin/')
            ->setLinker('ld64.lld');
    } else {
        $p->setLinker('lld');
    }
    $p->setLogicalProcessors('$(sysctl -n hw.ncpu)');
} else {
    $p->setLinker('ld.lld');
    $p->setLogicalProcessors('$(nproc 2> /dev/null)');
}

$p->setExtraCflags(' -Os');

// Generate make.sh
$p->execute();


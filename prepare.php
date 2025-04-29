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
    $p->setExtraLdflags('');
    $homebrew_prefix = trim(shell_exec('brew --prefix'));
    $p->withBinPath($homebrew_prefix . '/opt/llvm/bin')
        ->withBinPath($homebrew_prefix . '/opt/flex/bin')
        ->withBinPath($homebrew_prefix . '/opt/bison/bin')
        ->withBinPath($homebrew_prefix . '/opt/libtool/bin')
        ->withBinPath($homebrew_prefix . '/opt/m4/bin')
        ->withBinPath($homebrew_prefix . '/opt/automake/bin/')
        ->withBinPath($homebrew_prefix . '/opt/autoconf/bin/')
        ->withBinPath($homebrew_prefix . '/opt/gettext/bin')
        ->setLinker('ld64.lld');
    $p->setLogicalProcessors('$(sysctl -n hw.ncpu)');
} else {
    $p->setLinker('ld.lld');
    $p->setLogicalProcessors('$(nproc 2> /dev/null)');
}

$p->setExtraCflags(' -Os');

// Generate make.sh
$p->execute();


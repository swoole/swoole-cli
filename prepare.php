#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

use SwooleCli\Preprocessor;

# clean old make.sh
if (file_exists(__DIR__ . '/make.sh')) {
    unlink(__DIR__ . '/make.sh');
}
if (file_exists(__DIR__ . '/make-install-deps.sh')) {
    unlink(__DIR__ . '/make-install-deps.sh');
}
if (file_exists(__DIR__ . '/make-download-box.sh')) {
    unlink(__DIR__ . '/make-download-box.sh');
}

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

if ($p->getInputOption('with-override-default-enabled-ext')) {
    $p->setExtEnabled([]);
}

if ($p->getInputOption('with-global-prefix')) {
    $p->setGlobalPrefix($p->getInputOption('with-global-prefix'));
}

if ($p->getInputOption('with-parallel-jobs')) {
    $p->setMaxJob(intval($p->getInputOption('with-parallel-jobs')));
}

$buildType = $p->getBuildType();
if ($p->getInputOption('with-build-type')) {
    $buildType = $p->getInputOption('with-build-type');
    $p->setBuildType($buildType);
}
define('SWOOLE_CLI_BUILD_TYPE', $buildType);
define('SWOOLE_CLI_GLOBAL_PREFIX', $p->getGlobalPrefix());

if ($p->getInputOption('with-http-proxy')) {
    $http_proxy = $p->getInputOption('with-http-proxy');
    $proxyConfig = <<<EOF
export HTTP_PROXY={$http_proxy}
export HTTPS_PROXY={$http_proxy}
export NO_PROXY="127.0.0.0/8,10.0.0.0/8,100.64.0.0/10,172.16.0.0/12,192.168.0.0/16,198.18.0.0/15,169.254.0.0/16"
export NO_PROXY="\${NO_PROXY},127.0.0.1,localhost"
export NO_PROXY="\${NO_PROXY},.aliyuncs.com,.aliyun.com"
export NO_PROXY="\${NO_PROXY},.tsinghua.edu.cn,.ustc.edu.cn,.npmmirror.com"
export NO_PROXY="\${NO_PROXY},dl-cdn.alpinelinux.org,deb.debian.org,security.debian.org"
export NO_PROXY="\${NO_PROXY},archive.ubuntu.com,security.ubuntu.com"
export NO_PROXY="\${NO_PROXY},pypi.python.org,bootstrap.pypa.io"

EOF;
    $p->setProxyConfig($proxyConfig, $http_proxy);
}

if ($p->getInputOption('with-install-library-cached')) {
    $p->setInstallLibraryCached(true);
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

$p->setExtraCflags(' -Os');

// Generate make.sh
$p->execute();

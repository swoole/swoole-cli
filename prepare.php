<?php

require __DIR__ . '/vendor/autoload.php';

use SwooleCli\Preprocessor;

$php_version_tag = trim(file_get_contents(__DIR__ . '/sapi/PHP-VERSION.conf'));
define('BUILD_PHP_VERSION', $php_version_tag);

$homeDir = getenv('HOME');
$p = Preprocessor::getInstance();
$p->parseArguments($argc, $argv);


# clean old make.sh
if (file_exists(__DIR__ . '/make.sh')) {
    unlink(__DIR__ . '/make.sh');
    unlink(__DIR__ . '/make-install-deps.sh');
    unlink(__DIR__ . '/make-env.sh');
    unlink(__DIR__ . '/make-export-variables.sh');
}

if (file_exists(__DIR__ . '/make-download-box.sh')) {
    unlink(__DIR__ . '/make-download-box.sh');
}

// Sync code from php-src
$p->setPhpSrcDir($homeDir . '/.phpbrew/build/php-' . BUILD_PHP_VERSION);

// Compile directly on the host machine, not in the docker container
if ($p->getInputOption('without-docker') || ($p->isMacos())) {
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
EOF;
    $proxyConfig .= PHP_EOL;
    $proxyConfig .= <<<'EOF'
export NO_PROXY="127.0.0.0/8,10.0.0.0/8,100.64.0.0/10,172.16.0.0/12,192.168.0.0/16"
export NO_PROXY="${NO_PROXY},127.0.0.1,localhost"
export NO_PROXY="${NO_PROXY},.aliyuncs.com,.aliyun.com,.tencent.com"
export NO_PROXY="${NO_PROXY},.tsinghua.edu.cn,.ustc.edu.cn,.npmmirror.com"
export NO_PROXY="${NO_PROXY},dl-cdn.alpinelinux.org"
export NO_PROXY="${NO_PROXY},deb.debian.org,security.debian.org"
export NO_PROXY="${NO_PROXY},archive.ubuntu.com,security.ubuntu.com"
export NO_PROXY="${NO_PROXY},pypi.python.org,bootstrap.pypa.io"
export NO_PROXY="${NO_PROXY},.sourceforge.net"
export NO_PROXY="${NO_PROXY},.gitee.com"

EOF;
    $p->setProxyConfig($proxyConfig, $http_proxy);
}


if ($p->isMacos()) {
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

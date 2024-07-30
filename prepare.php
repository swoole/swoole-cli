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

# clean
# clean old make.sh
$p->cleanFile(__DIR__ . '/make.sh');
$p->cleanFile(__DIR__ . '/make-install-deps.sh');
$p->cleanFile(__DIR__ . '/make-env.sh');
$p->cleanFile(__DIR__ . '/make-export-variables.sh');
$p->cleanFile(__DIR__ . '/make-download-box.sh');
$p->cleanFile(__DIR__ . '/cppflags.log');
$p->cleanFile(__DIR__ . '/ldflags.log');
$p->cleanFile(__DIR__ . '/libs.log');
$p->cleanFile(__DIR__ . '/configure.backup');


// Sync code from php-src
$p->setPhpSrcDir($p->getWorkDir() . '/var/php-' . BUILD_PHP_VERSION);

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
NO_PROXY="127.0.0.0/8,10.0.0.0/8,100.64.0.0/10,172.16.0.0/12,192.168.0.0/16"
NO_PROXY="${NO_PROXY},::1/128,fe80::/10,fd00::/8,ff00::/8"
NO_PROXY="${NO_PROXY},.aliyuncs.com,.aliyun.com,.tencent.com"
NO_PROXY="${NO_PROXY},.tsinghua.edu.cn,.ustc.edu.cn,.npmmirror.com"
NO_PROXY="${NO_PROXY},ftpmirror.gnu.org"
NO_PROXY="${NO_PROXY},gitee.com,gitcode.com"
NO_PROXY="${NO_PROXY},.myqcloud.com,.swoole.com"
NO_PROXY="${NO_PROXY},dl-cdn.alpinelinux.org"
NO_PROXY="${NO_PROXY},deb.debian.org,security.debian.org"
NO_PROXY="${NO_PROXY},archive.ubuntu.com,security.ubuntu.com"
NO_PROXY="${NO_PROXY},pypi.python.org,bootstrap.pypa.io"
export NO_PROXY="${NO_PROXY},localhost"


EOF;
    $p->setProxyConfig($proxyConfig, $http_proxy);
}


if ($p->isMacos()) {
    $p->setExtraLdflags('-undefined dynamic_lookup');
    if (is_file('/usr/local/opt/llvm/bin/ld64.lld')) {
        $p->withBinPath('/usr/local/opt/llvm/bin')
            ->withBinPath('/opt/homebrew/opt/flex/bin')
            ->withBinPath('/opt/homebrew/opt/bison/bin')
            ->withBinPath('/opt/homebrew/opt/libtool/bin')
            ->withBinPath('/opt/homebrew/opt/m4/bin')->setLinker('ld64.lld');
    } elseif (is_file('/opt/homebrew/opt/llvm/bin/ld64.lld')) { //兼容 github action
        $p->withBinPath('/opt/homebrew/opt/llvm/bin/')->setLinker('ld64.lld');
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

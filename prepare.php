<?php

require __DIR__ . '/vendor/autoload.php';

use SwooleCli\Preprocessor;
use SwooleCli\Library;

$homeDir = getenv('HOME');
$p = Preprocessor::getInstance();
$p->parseArguments($argc, $argv);


# PHP 默认版本
$version = '8.2.4';

if ($p->getInputOption('with-php-version')) {
    $subject = $p->getInputOption('with-php-version');
    $pattern = '/(\d{1,2})\.\d{1,2}\.\d{1,2}/';
    if (preg_match($pattern, $subject, $match)) {
        if (intval($match[1]) >= 8) {
            $version = $match[0];
        } else {
            echo <<<EOF

    support  PHP7.4  PHP7.3 PHP8  micro

    php-8-micro:  (https://github.com/dixyes/phpmicro.git）

        git clone -b build_native_php_sfx_micro  https://github.com/jingjingxyk/swoole-cli/

    php-8.2:

        git clone -b build_php_8.2  https://github.com/jingjingxyk/swoole-cli/

    php-7.4:

        git clone -b build_php_7.4  https://github.com/jingjingxyk/swoole-cli/

    php-7.3:

        git clone -b build_php_7.3  https://github.com/jingjingxyk/swoole-cli/

EOF;
            echo PHP_EOL;
            if (is_file(__DIR__ . '/' . 'make.sh')) {
                unlink(__DIR__ . '/' . 'make.sh');
            }
            die;
        }
    }
}

define('BUILD_PHP_VERSION', $version);

// Compile directly on the host machine, not in the docker container
if ($p->getInputOption('without-docker') || ($p->getOsType() == 'macos')) {
    $p->setWorkDir(__DIR__);
    $p->setBuildDir(__DIR__ . '/thirdparty');
}


$p->setRootDir(__DIR__);

// Sync code from php-src
//设置 PHP 源码所在目录
$p->setPhpSrcDir($p->getRootDir() . '/php-src');

//设置PHP 安装目录
define("BUILD_PHP_INSTALL_PREFIX", $p->getRootDir() . '/bin/php-' . BUILD_PHP_VERSION);

if ($p->getInputOption('with-override-default-enabled-ext')) {
    $p->setExtEnabled([]);
}

if ($p->getInputOption('with-global-prefix')) {
    $p->setGlobalPrefix($p->getInputOption('with-global-prefix'));
}

$buildType = $p->getBuildType();

if ($p->getInputOption('with-build-type')) {
    $buildType = $p->getInputOption('with-build-type');
    $p->setBuildType($buildType);
}

define('PHP_CLI_BUILD_TYPE', $buildType);
define('PHP_CLI_GLOBAL_PREFIX', $p->getGlobalPrefix());

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
    define('PHP_CLI_HTTP_PROXY_URL', $http_proxy);
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
    $p->setProxyConfig($proxyConfig);
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


if ($p->getInputOption('with-c-compiler')) {
    $c_compiler = $p->getInputOption('with-c-compiler');
    if ($c_compiler == 'gcc') {
        $p->set_C_COMPILER('gcc');
        $p->set_CXX_COMPILER('g++');
        $p->setLinker('ld');
    }
}

$p->setExtraCflags('-fno-ident -Os');

$p->withPreInstallCommand('#!/usr/bin/env bash');
$p->withPreInstallCommand('set -x');

// Generate make.sh
$p->execute();


function install_libraries(Preprocessor $p): void
{
    $p->loadDependentLibrary('php_src');
}

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
        }
    }
}

define('BUILD_PHP_VERSION', $version);

// Compile directly on the host machine, not in the docker container
if ($p->getInputOption('without-docker') || ($p->getOsType() == 'macos')) {
    $p->setWorkDir(__DIR__);
    $p->setBuildDir(__DIR__ . '/thirdparty');
}

// Sync code from php-src
//设置 PHP 源码所在目录
$p->setPhpSrcDir($p->getWorkDir() . '/php-src');

//设置PHP 安装目录

define("BUILD_PHP_INSTALL_PREFIX", $p->getWorkDir() . '/bin/php-' . BUILD_PHP_VERSION);

if ($p->getInputOption('with-global-prefix')) {
    $p->setGlobalPrefix($p->getInputOption('with-global-prefix'));
}



//release 版本，屏蔽这两个函数，使其不生效
// ->withCleanBuildDirectory()
// ->withCleanPreInstallDirectory($prefix)
//SWOOLE_CLI_SKIP_DEPEND_DOWNLOAD
//SWOOLE_CLI_BUILD_TYPE=release

if ($p->getInputOption('with-global-prefix')) {
    $p->setGlobalPrefix($p->getInputOption('with-global-prefix'));
}

$buildType= $p->getBuildType();
if ($p->getInputOption('with-build-type')) {
    $buildType=$p->getInputOption('with-build-type');
    $p->setBuildType($buildType);
}

define('PHP_CLI_BUILD_TYPE', $buildType);
define('PHP_CLI_GLOBAL_PREFIX', $p->getGlobalPrefix());

if ($p->getInputOption('with-parallel-jobs')) {
    $p->setMaxJob(intval($p->getInputOption('with-parallel-jobs')));
}

if ($p->getOsType() == 'macos') {
    // -lintl -Wl,-framework -Wl,CoreFoundation
    //$p->setExtraLdflags('-framework CoreFoundation -framework SystemConfiguration -undefined dynamic_lookup');
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

# 设置CPU核数 ; 获取CPU核数，用于 make -j $(nproc)
# $p->setMaxJob(`nproc 2> /dev/null || sysctl -n hw.ncpu`); // nproc on macos ；
# `grep "processor" /proc/cpuinfo | sort -u | wc -l`


if ($p->getOsType() == 'macos') {
    $p->addEndCallback(function () use ($p) {
        $header = <<<'EOF'
export PATH=/opt/homebrew/bin/:/usr/local/bin/:$PATH

export HOMEBREW_INSTALL_FROM_API=1
export HOMEBREW_API_DOMAIN="https://mirrors.ustc.edu.cn/homebrew-bottles/api"

export HOMEBREW_BREW_GIT_REMOTE="https://mirrors.ustc.edu.cn/brew.git"
export HOMEBREW_CORE_GIT_REMOTE="https://mirrors.ustc.edu.cn/homebrew-core.git"
export HOMEBREW_BOTTLE_DOMAIN="https://mirrors.ustc.edu.cn/homebrew-bottles"

export HOMEBREW_PIP_INDEX_URL="https://pypi.tuna.tsinghua.edu.cn/simple"
export HOMEBREW_NO_ANALYTICS=1
export HOMEBREW_NO_AUTO_UPDATE=1
EOF;
        $command = file_get_contents(__DIR__ . '/make.sh');
        $command = $header . PHP_EOL . $command;
        file_put_contents(__DIR__ . '/make.sh', $command);
    });
}


$cmd = '';
if ($p->getOsType() == 'macos') {
    $cmd .= <<<'EOF'
brew=$(which brew  | wc -l)
if test $brew -eq 1 ;then
{
    meson=$(which meson  | wc -l)
    if test $meson -ne  1 ;then
    {
        export HOMEBREW_API_DOMAIN="https://mirrors.tuna.tsinghua.edu.cn/homebrew-bottles/api"
        export HOMEBREW_BREW_GIT_REMOTE="https://mirrors.tuna.tsinghua.edu.cn/git/homebrew/brew.git"
        # export HOMEBREW_BREW_GIT_REMOTE="https://mirrors.ustc.edu.cn/brew.git"
        brew install ninja  python3 gn zip unzip 7zip lzip go flex
        pip3 install meson virtualenv -i https://pypi.tuna.tsinghua.edu.cn/simple
    }
    fi
}
fi


EOF;
}
if ($p->getOsType() == 'linux') {
    $cmd .= <<<'EOF'
if test -f /etc/os-release; then
{
    alpine=$(cat /etc/os-release | grep "ID=alpine" | wc -l)
    if test $alpine -eq 1  ;then
    {
        meson=$(which meson | wc -l )
        if test $meson -ne 1 ;then
        {
             apk add ninja python3 py3-pip gn zip unzip p7zip lzip  go flex
             pip3 install meson virtualenv pipenv -i https://pypi.tuna.tsinghua.edu.cn/simple
             # git config --global --add safe.directory /work

        }
        fi
    }
    fi
}
fi

EOF;
}


$p->addEndCallback(function () use ($p, $cmd) {
    $header = <<<'EOF'
#!/bin/env bash

export cpu_nums=`nproc 2> /dev/null || sysctl -n hw.ncpu`
# `grep "processor" /proc/cpuinfo | sort -u | wc -l`

EOF;

    $command = file_get_contents(__DIR__ . '/make.sh');
    $command = $header . PHP_EOL . $cmd . PHP_EOL . $command;
    file_put_contents(__DIR__ . '/make.sh', $command);
});


$p->setExtraCflags('-fno-ident -Os');



// Generate make.sh
$p->execute();


function install_libraries(Preprocessor $p): void
{
    $p->loadDependentLibrary('php_src');
}

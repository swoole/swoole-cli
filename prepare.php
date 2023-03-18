#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';

use SwooleCli\Preprocessor;

$homeDir = getenv('HOME');
$p = Preprocessor::getInstance();
$p->parseArguments($argc, $argv);

// Sync code from php-src
$p->setPhpSrcDir($homeDir . '/.phpbrew/build/php-8.1.12');

// Compile directly on the host machine, not in the docker container
if ($p->getInputOption('without-docker')) {
    $p->setWorkDir(__DIR__);
    $p->setBuildDir(__DIR__ . '/thirdparty');
    $p->setGlobalPrefix($homeDir . '/.swoole-cli');
}

if ($p->getOsType() == 'macos') {
    // -lintl -Wl,-framework -Wl,CoreFoundation
    //$p->setExtraLdflags('-framework CoreFoundation -framework SystemConfiguration -undefined dynamic_lookup');
    $p->setExtraLdflags('-undefined dynamic_lookup');
}



# 设置CPU核数 ; 获取CPU核数，用于 make -j $(nproc)
# $p->setMaxJob(`nproc 2> /dev/null || sysctl -n hw.ncpu`); // nproc on macos ；
# `grep "processor" /proc/cpuinfo | sort -u | wc -l`

//release 版本，屏蔽这两个函数，使其不生效
// ->withCleanBuildDirectory()
// ->withCleanPreInstallDirectory($prefix)
//SWOOLE_CLI_SKIP_DEPEND_DOWNLOAD
//SWOOLE_CLI_BUILD_TYPE=release
if ($p->getInputOption('with-build-type') == 'release') {
    define('SWOOLE_CLI_BUILD_TYPE', 1);
} else {
    define('SWOOLE_CLI_BUILD_TYPE', 0);
}

if ($p->getOsType() == 'macos') {
    $p->addEndCallback(function () use ($p) {
        $header=<<<'EOF'
export PATH=/opt/homebrew/bin/:/usr/local/bin/:$PATH
EOF;
        $command= file_get_contents(__DIR__ . '/make.sh');
        $command=$header.PHP_EOL.$command;
        file_put_contents(__DIR__ . '/make.sh', $command);
    });
}



$cmd ='';
if ($p->getOsType() == 'macos') {
    $cmd .=<<<'EOF'
brew=$(which brew  | wc -l)
if test $brew -eq 1 ;then
{
    meson=$(which meson  | wc -l)
    if test $meson -ne  1 ;then
    {
        export HOMEBREW_API_DOMAIN="https://mirrors.tuna.tsinghua.edu.cn/homebrew-bottles/api"
        export HOMEBREW_BREW_GIT_REMOTE="https://mirrors.tuna.tsinghua.edu.cn/git/homebrew/brew.git"
        # export HOMEBREW_BREW_GIT_REMOTE="https://mirrors.ustc.edu.cn/brew.git"
        brew install ninja  python3 gn zip unzip 7zip lzip
        pip3 install meson virtualenv -i https://pypi.tuna.tsinghua.edu.cn/simple
    }
    fi
}
fi


EOF;
}
if ($p->getOsType() == 'linux') {
    $cmd .=<<<'EOF'
if test -f /etc/os-release; then
{
    alpine=$(cat /etc/os-release | grep "ID=alpine" | wc -l)
    if test $alpine -eq 1  ;then
    {
        meson=$(which meson | wc -l )
        if test $meson -ne 1 ;then
        {
             apk add ninja python3 py3-pip gn zip unzip 7zip lzip
             pip3 install meson -i https://pypi.tuna.tsinghua.edu.cn/simple
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
    $header=<<<'EOF'
#!/bin/env sh

PKG_CONFIG_PATH='/usr/lib/pkgconfig'
test -d /usr/lib64/pkgconfig && PKG_CONFIG_PATH="/usr/lib64/pkgconfig:$PKG_CONFIG_PATH" ;
test -d /usr/local/lib64/pkgconfig && PKG_CONFIG_PATH="/usr/local/lib64/pkgconfig:$PKG_CONFIG_PATH" ;
test -d /usr/local/lib/pkgconfig/ && PKG_CONFIG_PATH="/usr/local/lib/pkgconfig/:$PKG_CONFIG_PATH" ;

cpu_nums=`nproc 2> /dev/null || sysctl -n hw.ncpu`
# `grep "processor" /proc/cpuinfo | sort -u | wc -l`


EOF;

    $command= file_get_contents(__DIR__ . '/make.sh');
    $command= $header. PHP_EOL . $cmd . PHP_EOL . $command ;
    file_put_contents(__DIR__ . '/make.sh', $command);
});


$p->setExtraCflags('-fno-ident -Os');




// Generate make.sh
$p->execute();

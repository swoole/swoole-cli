#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';

use SwooleCli\Preprocessor;
define('SUPPPER_SKIP',0);
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
    $p->setExtraLdflags('-framework CoreFoundation -framework SystemConfiguration -undefined dynamic_lookup');
    // fix "checking for curl_easy_perform in -lcurl"
    $p->setConfigureVarables('LDFLAGS="-framework CoreFoundation -framework SystemConfiguration"');
}



# 设置CPU核数 ; 获取CPU核数，用于 make -j $(nproc)
# $p->setMaxJob(`nproc 2> /dev/null || sysctl -n hw.ncpu`); // nproc on macos ；
# `grep "processor" /proc/cpuinfo | sort -u | wc -l`




if ($p->getOsType() == 'macos') {

    $p->addEndCallback(function () use ($p) {
        $header=<<<'EOF'
export PATH=/opt/homebrew/bin/:/usr/local/bin/:$PATH
EOF;
        $command= file_get_contents(__DIR__ . '/make.sh');
        $command=$header.PHP_EOL.$command;
        file_put_contents(__DIR__ . '/make.sh',$command);
    });

}


$p->addEndCallback(function () use ($p) {
    $header=<<<'EOF'
#!/bin/env sh


PKG_CONFIG_PATH='/usr/lib/pkgconfig'
test -d /usr/lib64/pkgconfig && PKG_CONFIG_PATH="/usr/lib64/pkgconfig:$PKG_CONFIG_PATH" ;
test -d /usr/local/lib64/pkgconfig && PKG_CONFIG_PATH="/usr/local/lib64/pkgconfig:$PKG_CONFIG_PATH" ;
test -d /usr/local/lib/pkgconfig/ && PKG_CONFIG_PATH="/usr/local/lib/pkgconfig/:$PKG_CONFIG_PATH" ;


cpu_nums=`nproc 2> /dev/null || sysctl -n hw.ncpu`
# `grep "processor" /proc/cpuinfo | sort -u | wc -l`

export PATH=/usr/ninja/bin/:$PATH

EOF;

    $command= file_get_contents(__DIR__ . '/make.sh');
    $command=$header.PHP_EOL.$command;
    file_put_contents(__DIR__ . '/make.sh',$command);
});


$p->setExtraCflags('-fno-ident -Os');

// Generate make.sh
$p->execute();
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
    $p->setExtraLdflags('-framework CoreFoundation -framework SystemConfiguration -undefined dynamic_lookup');
}




# $p->setMaxJob(`nproc 2> /dev/null || sysctl -n hw.ncpu`);
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
set -uex
PKG_CONFIG_PATH='/usr/lib/pkgconfig'
test -d /usr/lib64/pkgconfig && PKG_CONFIG_PATH="/usr/lib64/pkgconfig:$PKG_CONFIG_PATH" ;
test -d /usr/local/lib64/pkgconfig && PKG_CONFIG_PATH="/usr/local/lib64/pkgconfig:$PKG_CONFIG_PATH" ;

cpu_nums=`nproc 2> /dev/null || sysctl -n hw.ncpu`
# `grep "processor" /proc/cpuinfo | sort -u | wc -l`

EOF;
    $command= file_get_contents(__DIR__ . '/make.sh');
    $command=$header.PHP_EOL.$command;
    file_put_contents(__DIR__ . '/make.sh',$command);
});



install_libiconv($p);//没有 libiconv.pc 文件 不能使用 pkg-config 命令
install_openssl($p);
install_libxml2($p); //依赖 libiconv
install_libxslt($p); //依赖 libxml2 libiconv
install_gmp($p);
install_zlib($p);//->depends('libxml2', 'bzip2')
install_bzip2($p);//没有 libbz2.pc 文件，不能使用 pkg-config 命令
install_libgif($p);//没有 libgif.pc 文件，不能使用 pkg-config 命令
install_libpng($p); //依赖 zlib
install_libjpeg($p);
install_harfbuzz($p); //默认跳过安装
install_libwebp($p); //依赖 libgif libpng libjpeg
install_freetype($p); //依赖 zlib bzip2 libpng  brotli(暂不启用)  HarfBuzz (暂不启用)
install_sqlite3($p);
install_icu($p); //依赖  -lstdc++
install_oniguruma($p);
install_liblz4($p);
install_liblzma($p);
install_libzstd($p); //zstd 依赖 lz4
install_zip($p); //zip 依赖 openssl zlib bzip2  liblzma zstd 静态库 (liblzma库 zstd库 暂不启用）
install_brotli($p);
install_cares($p);
//install_libedit($p);
install_ncurses($p);
install_readline($p);//依赖 ncurses
install_imagemagick($p);//依赖 freetype png webp xml zip zlib
install_libidn2($p);  //默认跳过安装
install_nghttp2($p);  //默认跳过安装
install_curl($p); //curl 依赖 openssl brotli(暂不启用) zstd(暂不启用) idn(暂不启用) idn2(暂不启用) nghttp2(暂不启用) nghttp3(暂不启用)
install_libsodium($p);
install_libyaml($p);
install_mimalloc($p);
install_pgsql($p);//依赖 openssl libxml2 libxslt  zlib readline icu libxml2 libxslt
install_libffi($p);

install_php_internal_extensions($p); //安装内置扩展; ffi  pgsql pdo_pgsql

install_php_extension_micro($p);

if ($p->getOsType() == 'macos') {
    install_bison($p);  // 源码编译bison
    install_php_internal_extension_curl_patch($p);  //修改 `ext/curl/config.m4` ，去掉 `HAVE_CURL` 检测
}

if ($p->getOsType() == 'win') {
    install_re2c($p);
}

# 扩展 mbstring 依赖 oniguruma 库
# 扩展 intl 依赖 ICU 库
# 扩展 gd 依赖 libpng，freetype 库 ；  freetype 依赖 zlib bzip2 libpng  brotli 等;  libwebp 依赖 giflib
# 扩展 mongodb 依赖 openssl, zlib, ICU 等库
# 本项目 opcache 是必装扩展，否则编译报错，不想启用opcache，需要修改源码: main/main.c
# 本项目 swoole  是必装扩展，否则 sh make.sh archive 无法打包

# php7 不支持openssl V3 ，PHP8 支持openssl V3 , openssl V3 默认库目录 /usr/openssl/lib64

# label: build_env_bin , php_extension_patch , php_internal_extension , php_extension ,extension_library
#
/**
    # 需要特别设置的地方

    export  CPPFLAGS=$(pkg-config  --cflags --static  libpq libcares libffi icu-uc icu-io icu-i18n readline )
    LIBS=$(pkg-config  --libs --static   libpq libcares libffi icu-uc icu-io icu-i18n readline )
    export LIBS="$LIBS -L/usr/lib -lstdc++"

 */


// Generate make.sh
$p->execute();
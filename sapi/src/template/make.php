<?php
/**
 * @var $this SwooleCli\Preprocessor
 */

use SwooleCli\Preprocessor;

?>
#!/usr/bin/env bash
__PROJECT_DIR__=$(cd "$(dirname "$0")"; pwd)
CLI_BUILD_TYPE=<?= $this->getBuildType() . PHP_EOL ?>
SRC=<?= $this->phpSrcDir . PHP_EOL ?>
ROOT=<?= $this->getRootDir() . PHP_EOL ?>
PREPARE_ARGS="<?= implode(' ', $this->getPrepareArgs())?>"
export LOGICAL_PROCESSORS=<?= trim($this->logicalProcessors). PHP_EOL ?>
export CMAKE_BUILD_PARALLEL_LEVEL=<?= $this->maxJob. PHP_EOL ?>
<?php if ($this->getOsType() == 'linux') : ?>
export OS_RELEASE=$(awk -F= '/^ID=/{print $2}' /etc/os-release |tr -d '\n' | tr -d '\"')
<?php else : ?>
export OS_RELEASE='macos'
<?php endif ; ?>

export CC=<?= $this->cCompiler . PHP_EOL ?>
export CXX=<?= $this->cppCompiler . PHP_EOL ?>
export LD=<?= $this->lld . PHP_EOL ?>


export SYSTEM_ORIGIN_PKG_CONFIG_PATH=$PKG_CONFIG_PATH
export PKG_CONFIG_PATH=<?= implode(':', $this->pkgConfigPaths) . PHP_EOL ?>
export SWOOLE_CLI_PKG_CONFIG_PATH=$PKG_CONFIG_PATH

export SYSTEM_ORIGIN_PATH=$PATH
export PATH=<?= implode(':', $this->binPaths) . PHP_EOL ?>
export SWOOLE_CLI_PATH=$PATH

# 参考： https://www.php.net/manual/en/install.pecl.static.php

OPTIONS="--disable-all \
--disable-cgi  \
--enable-shared=no \
--enable-static=yes \
--without-valgrind \
--enable-cli  \
--disable-phpdbg \
<?php foreach ($this->extensionList as $item) : ?>
    <?=$item->options?> \
<?php endforeach; ?>
<?=$this->extraOptions?>
"

<?php foreach ($this->libraryList as $item) : ?>
make_<?=$item->name?>() {
    echo "build <?=$item->name?>"

    <?php if (in_array($this->buildType, ['dev', 'debug'])) : ?>
    set -x
    <?php endif ;?>

    <?php if ($item->skipBuildInstall) : ?>
        echo "skip install library <?=$item->name?>" ;
        return 0 ;
    <?php endif ;?>

    <?php if ($item->enableInstallCached) : ?>
    if [ -f <?= $this->getGlobalPrefix() . '/'.  $item->name ?>/.completed ] ;then
        echo "[<?=$item->name?>]  library cached , skip.."
        return 0
    fi
    <?php endif; ?>

    # 默认不需要，当需要构建中间库时需要
    <?php if ($item->enableCompiledCached) : ?>
    if [ -f <?=$this->getBuildDir()?>/<?=$item->name?>/.completed  ]; then
        echo "[<?=$item->name?>] compiled, skip.."
        cd <?= $this->workDir ?>/
        return 0
    fi
    <?php endif; ?>

    <?php if ($item->cleanBuildDirectory || !$item->enableBuildCached) : ?>
    if [ -d <?=$this->getBuildDir()?>/<?=$item->name?>/ ]; then
        rm -rf <?=$this->getBuildDir()?>/<?=$item->name?>/
    fi
    <?php endif; ?>

    # If the source code directory does not exist, create a directory and decompress the source code archive
    if [ ! -d <?=$this->getBuildDir()?>/<?=$item->name?> ]; then
        mkdir -p <?=$this->getBuildDir()?>/<?=$item->name . PHP_EOL?>
    fi

    <?php if ($item->untarArchiveCommand == 'tar') : ?>
    tar --strip-components=1 -C <?=$this->getBuildDir()?>/<?=$item->name?> -xf <?=$this->workDir?>/pool/lib/<?=$item->file . PHP_EOL?>
    result_code=$?
    if [ $result_code -ne 0 ]; then
        echo "[<?=$item->name?>] [configure FAILURE]"
        rm -rf <?=$this->getBuildDir()?>/<?=$item->name?>/
        exit  $result_code
    fi
    <?php endif ; ?>

    <?php if ($item->untarArchiveCommand == 'unzip') : ?>
    unzip -d  <?=$this->getBuildDir()?>/<?=$item->name?>   <?=$this->workDir?>/pool/lib/<?=$item->file?> <?= PHP_EOL; ?>
    <?php endif ; ?>
    <?php if ($item->untarArchiveCommand == 'xz') :?>
    xz -f -d -k   <?=$this->workDir?>/pool/lib/<?=$item->file?>    <?= PHP_EOL; ?>
    tar --strip-components=1 -C <?=$this->getBuildDir()?>/<?=$item->name?> -xf <?= rtrim($this->workDir . '/pool/lib/' . $item->file, '.xz') . PHP_EOL?>
    <?php endif ; ?>
    <?php if ($item->untarArchiveCommand == 'cp') :  ?>
    cp -rfa  <?=$this->workDir?>/pool/lib/<?=$item->file?>/* <?=$this->getBuildDir()?>/<?=$item->name?>/   <?= PHP_EOL; ?>
    <?php endif ; ?>
    <?php if ($item->untarArchiveCommand == 'mv') :  ?>
    cp -rfa  <?=$this->workDir?>/pool/lib/<?=$item->file?> <?=$this->getBuildDir()?>/<?=$item->name?>/    <?= PHP_EOL; ?>
    <?php endif ; ?>

    <?php if ($item->cleanPreInstallDirectory) : ?>
    # If the install directory exist, clean the install directory
    test -d <?=$item->preInstallDirectory?>/ && rm -rf <?=$item->preInstallDirectory?>/ ;
    <?php endif; ?>

    cd <?=$this->getBuildDir()?>/<?=$item->name . PHP_EOL?>

    <?php if ($item->enableEnv) : ?>
    if [  -f <?= $this->getWorkDir() ?>/.env ] ; then
        for line in `cat <?= $this->getWorkDir() ?>/.env`
        do
            echo $line
            export $line
        done
    fi
    <?php endif;?>

    <?php if ($item->enableSystemOriginEnvPath) : ?>
    export PKG_CONFIG_PATH=${SYSTEM_ORIGIN_PKG_CONFIG_PATH}
    export PATH=${SYSTEM_ORIGIN_PATH}
    <?php endif;?>

    <?php if ($item->enableSystemHttpProxy && !empty($this->getHttpProxy())) : ?>
    mkdir -p /etc/apt/apt.conf.d/

    cat > /etc/apt/apt.conf.d/proxy.conf <<'--OS-PROXY-<?=$item->name?>-EOF--'
    Acquire::http::Proxy  "<?= $this->getHttpProxy() ?>";
    Acquire::https::Proxy "<?= $this->getHttpProxy() ?>";

--OS-PROXY-<?=$item->name?>-EOF--
    <?php endif;?>

    <?php if ($item->enableBuildLibraryHttpProxy && $this->getProxyConfig()) : ?>
        <?= $this->getProxyConfig() . PHP_EOL ?>
        <?php if ($item->enableBuildLibraryGitProxy) :?>
            <?= $this->getGitProxyConfig() . PHP_EOL ?>
        <?php endif;?>
    <?php endif;?>

    # use build script replace  configure、make、make install
    <?php if (empty($item->buildScript)) : ?>
    # before configure
        <?php if (!empty($item->beforeConfigureScript)) : ?>
            <?= $item->beforeConfigureScript . PHP_EOL ?>
    result_code=$?
    [[ $result_code -gt 1 ]] &&  echo "[ before configure FAILURE]" && exit $result_code;
        <?php endif; ?>


    # configure
        <?php if (!empty($item->configure)) : ?>
    cat <<'___<?=$item->name?>__EOF___'
            <?= $item->configure . PHP_EOL ?>
___<?=$item->name?>__EOF___
            <?=$item->configure . PHP_EOL ?>
    result_code=$?
    [[ $result_code -ne 0 ]] &&  echo "[<?=$item->name?>] [configure FAILURE]" && exit  $result_code;
        <?php endif; ?>


    # make
    make -j <?= $this->maxJob ?> <?= $item->makeOptions . PHP_EOL ?>
    result_code=$?
    [[ $result_code -ne 0 ]] &&  echo "[<?=$item->name?>] [make FAILURE]" && exit  $result_code;

    # before make install
        <?php if ($item->beforeInstallScript) : ?>
            <?=$item->beforeInstallScript . PHP_EOL ?>
    result_code=$?
    [[ $result_code -ne 0 ]] &&  echo "[<?=$item->name?>] [ before make install script FAILURE]" && exit  $result_code;
        <?php endif; ?>

    # make install
        <?php if ($item->makeInstallCommand) : ?>
    make <?= $item->makeInstallCommand ?> <?= $item->makeInstallOptions ?> <?= PHP_EOL ?>
    result_code=$?
    [[ $result_code -ne 0 ]] &&  echo "[<?=$item->name?>] [make install FAILURE]" && exit  $result_code;

        <?php endif; ?>
    <?php else : ?>
    # use build script replace  configure、make、make install
    cat <<'___<?=$item->name?>__EOF___'
        <?= $item->buildScript . PHP_EOL ?>
___<?=$item->name?>__EOF___
        <?= $item->buildScript . PHP_EOL ?>
    result_code=$?
    [[ $result_code -ne 0 ]] &&  echo "[<?=$item->name?>] [build script FAILURE]" && exit  $result_code;
    <?php endif; ?>

    # after make install
    <?php if ($item->afterInstallScript) : ?>
        <?=$item->afterInstallScript . PHP_EOL ?>
    result_code=$?
    [[ $result_code -ne 0 ]] &&  echo "[<?=$item->name?>] [ after make  install script FAILURE]" && exit  $result_code;
    <?php endif; ?>

    # build end
    <?php if ($item->enableBuildLibraryHttpProxy) :?>
    unset HTTP_PROXY
    unset HTTPS_PROXY
    unset NO_PROXY
        <?php if ($item->enableBuildLibraryGitProxy) :?>
    unset GIT_PROXY_COMMAND
        <?php endif;?>
    <?php endif;?>

    <?php if ($item->enableSystemHttpProxy) : ?>
    test -f /etc/apt/apt.conf.d/proxy.conf && rm -rf /etc/apt/apt.conf.d/proxy.conf
    <?php endif;?>

    <?php if ($item->enableInstallCached) : ?>
    if [ -d <?= $this->getGlobalPrefix() . '/'.  $item->name ?>/ ] ;then
        touch <?= $this->getGlobalPrefix() . '/'.  $item->name ?>/.completed
    fi
    <?php endif; ?>

    <?php if ($item->enableSystemOriginEnvPath) : ?>
    export PKG_CONFIG_PATH=${SWOOLE_CLI_PKG_CONFIG_PATH}
    export PATH=${SWOOLE_CLI_PATH}
    <?php endif;?>

    <?php if ($item->enableCompiledCached) : ?>
    touch <?=$this->getBuildDir()?>/<?=$item->name?>/.completed
    <?php endif; ?>

    <?php if (in_array($this->buildType, ['dev', 'debug'])) : ?>
    set +x
    <?php endif ;?>

    cd <?= $this->workDir . PHP_EOL ?>
    return 0
}

clean_<?=$item->name?>() {
    cd <?=$this->getBuildDir()?> && echo "clean <?=$item->name?>"
    if [ -d <?=$this->getBuildDir()?>/<?= $item->name ?>/ ] ;then
        rm -rf <?=$this->getBuildDir()?>/<?= $item->name ?>/
    fi
    if [ -d <?=$this->getGlobalPrefix()?>/<?=$item->name?>/ ] ;then
        rm -rf <?=$this->getGlobalPrefix()?>/<?=$item->name?>/
    fi
    cd <?= $this->workDir . PHP_EOL ?>
    return 0
}

clean_<?=$item->name?>_cached() {
    echo "clean <?=$item->name?> [cached]"
    if [ -f <?=$this->getGlobalPrefix()?>/<?=$item->name?>/.completed ] ;then
        rm -f <?=$this->getGlobalPrefix()?>/<?=$item->name?>/.completed
    fi
    cd <?= $this->workDir . PHP_EOL ?>
    return 0
}

    <?php echo str_repeat(PHP_EOL, 1);?>
<?php endforeach; ?>

make_all_library() {
<?php if ($this->inVirtualMachine): ?>
    if [! -d <?= $this->getGlobalPrefix() ?>/sources ] ;then
        mkdir -p <?= $this->getGlobalPrefix() ?>/sources
        rm -rf <?= $this->getBuildDir() . PHP_EOL ?>
        ln -s <?= $this->getGlobalPrefix() ?>/sources <?= $this->getBuildDir() . PHP_EOL ?>
    fi
<?php endif; ?>
<?php foreach ($this->libraryList as $item) : ?>
    make_<?= $item->name ?> && [[ $? -eq 0 ]] && echo "[SUCCESS] make <?= $item->name ?>"
<?php endforeach; ?>
    return 0
}


before_configure_script() {
    cd <?= $this->getWorkDir() ?>/
<?php foreach ($this->beforeConfigure as $name => $value) : ?>
    # ext <?= $name ?> hook
    <?= $value($this) . PHP_EOL ?>
<?php endforeach; ?>
    cd <?= $this->getWorkDir() ?>/
    return 0
}

export_variables() {
    set -x

    CPPFLAGS=""
    CXXFLAGS=""
    CFLAGS=""
    LDFLAGS=""
    LIBS=""
    <?php if ($this->getOsType() == 'macos') :?>
    LDFLAGS="  -fuse-ld=lld"
    LDFLAGS="  -fuse-ld=ld64.lld"
    LDFLAGS="  -fuse-ld=ld"
    #   LDFLAGS="-L/usr/local/opt/llvm/lib/c++ -Wl,-rpath,/usr/local/opt/llvm/lib/c++"
    #   export LDFLAGS="-L/usr/local/opt/llvm/lib"
    #   export CPPFLAGS="-I/usr/local/opt/llvm/include"
    #   /usr/local/opt/llvm/bin
    <?php endif;?>

<?php foreach ($this->variables as $name => $value) : ?>
    <?= key($value) ?>="<?= current($value) ?>"
<?php endforeach; ?>
    result_code=$?
    [[ $result_code -ne 0 ]] &&  echo " [ export_variables  FAILURE ]" && exit  $result_code;
    echo "export variables"
<?php foreach ($this->exportVariables as $value) : ?>
    export <?= key($value) ?>="<?= current($value) ?>"
<?php endforeach; ?>

<?php if ($this->hasExtension('opcache')):?>
    export CFLAGS="$CFLAGS -DPHP_ENABLE_OPCACHE"
    export CPPFLAGS="$CPPFLAGS -DPHP_ENABLE_OPCACHE"
<?php endif; ?>
<?php if ($this->hasExtension('phpy')):?>
    CPPFLAGS="$CPPFLAGS -I<?= $this->getWorkDir() ?>/ext/phpy/include "
<?php endif; ?>
    export CPPFLAGS=$(echo $CPPFLAGS | tr ' ' '\n' | sort | uniq | tr '\n' ' ')
    export CXXFLAGS=$(echo $CXXFLAGS | tr ' ' '\n' | sort | uniq | tr '\n' ' ')
    export CFLAGS=$(echo $CFLAGS | tr ' ' '\n' | sort | uniq | tr '\n' ' ')
    export LDFLAGS=$(echo $LDFLAGS | tr ' ' '\n' | sort | uniq | tr '\n' ' ')
    export LIBS=$(echo $LIBS | tr ' ' '\n' | sort | uniq | tr '\n' ' ')
<?php if ($this->isLinux() && ($this->get_C_COMPILER() == 'musl-gcc')) : ?>
    ln -sf /usr/include/linux/ /usr/include/x86_64-linux-musl/linux
    ln -sf /usr/include/x86_64-linux-gnu/asm/ /usr/include/x86_64-linux-musl/asm
    ln -sf /usr/include/asm-generic/ /usr/include/x86_64-linux-musl/asm-generic

    export LDFLAGS="${LDFLAGS} -static -L/usr/lib/x86_64-linux-musl "

<?php endif ;?>
    result_code=$?
    [[ $result_code -ne 0 ]] &&  echo " [ export_variables  FAILURE ]" && exit  $result_code;
    set +x
    return 0
}

make_release_archive() {
    set -eux
    cd <?= $this->getWorkDir() ?>/
    <?php foreach ($this->releaseArchives as $name => $value) : ?>
        # ext <?= $name ?> hook
        <?= $value($this) . PHP_EOL ?>
    cd <?= $this->getWorkDir() ?>/
    <?php endforeach; ?>
    return 0
}

filter_extension() {
    cd <?= $this->phpSrcDir ?>/

    PHP_SRC_EXT_DIR=<?= $this->phpSrcDir ?>/ext/

    test -d /tmp/php-src-ext && rm -rf /tmp/php-src-ext
    mv $PHP_SRC_EXT_DIR /tmp/php-src-ext
    mkdir -p $PHP_SRC_EXT_DIR
    cd /tmp/php-src-ext
    test -d date && cp -rf date $PHP_SRC_EXT_DIR
    test -d hash && cp -rf hash $PHP_SRC_EXT_DIR
    test -d json && cp -rf json $PHP_SRC_EXT_DIR
    test -d pcre && cp -rf pcre $PHP_SRC_EXT_DIR
    test -d standard   && cp -rf standard $PHP_SRC_EXT_DIR
    test -d reflection && cp -rf reflection $PHP_SRC_EXT_DIR
    test -d spl        && cp -rf spl $PHP_SRC_EXT_DIR
    test -d tokenizer  && cp -rf tokenizer $PHP_SRC_EXT_DIR
    test -d session    && cp -rf session $PHP_SRC_EXT_DIR
    test -d random     && cp -rf random $PHP_SRC_EXT_DIR
    test -d phar       && cp -rf phar $PHP_SRC_EXT_DIR
<?php foreach ($this->extensionList as $value) : ?>
    test -d <?= $value->name ?> && cp -rf <?= $value->name ?> $PHP_SRC_EXT_DIR
<?php endforeach; ?>
    cd <?= $this->phpSrcDir ?>/
}

make_config() {

    exit 0

    cd <?= $this->phpSrcDir ?>/
<?php if (in_array($this->buildType, ['dev'])) : ?>
    # dev 环境 过滤扩展，便于调试单个扩展编译
    filter_extension
<?php endif ;?>

    cd <?= $this->phpSrcDir ?>/
    # 添加扩展
    if [ ! -z  "$(ls -A ${__PROJECT_DIR__}/ext/)" ] ;then
        cp -rf ${__PROJECT_DIR__}/ext/*  <?= $this->phpSrcDir ?>/ext/
    fi
    # 对扩展源代码执行预处理
    before_configure_script

    export_variables
    echo $LDFLAGS > <?= $this->getRootDir() ?>/ldflags.log
    echo $CPPFLAGS > <?= $this->getRootDir() ?>/cppflags.log
    echo $LIBS > <?= $this->getRootDir() ?>/libs.log

    ./configure --help

    ./configure $OPTIONS

:<<'_____EO_____'
    = 是最基本的赋值
    := 是覆盖之前的值
    ?= 是如果没有被赋值过就赋予等号后面的值
    += 是添加等号后面的值

    # GNU C编译器的gnu11和c11 https://www.cnblogs.com/litifeng/p/8328499.html
    # -g是生成调试信息
    # -Wall 是打开警告开关,-O代表默认优化,可选：-O0不优化,-O1低级优化,-O2中级优化,-O3高级优化,-Os代码空间优化

    # PKG_CONFIG_LIBDIR

    # 更多配置
    export EXTRA_INCLUDES=
    export EXTRA_CFLAGS
    export EXTRA_LDFLAGS=
    export EXTRA_LDFLAGS_PROGRAM=
    export EXTRA_LIBS=
    export ZEND_EXTRA_LIBS=


    export   CAPSTONE_CFLAGS="<?=$this->getGlobalPrefix()?>/capstone/include"
    export   CAPSTONE_LIBS="<?=$this->getGlobalPrefix()?>/capstone/lib"

    export   OPENSSL_CFLAGS=$(pkg-config --cflags --static libcrypto libssl    openssl)
    export   OPENSSL_LIBS=$(pkg-config   --libs   --static libcrypto libssl    openssl)

    export   NCURSES_CFLAGS=$(pkg-config --cflags --static  ncurses ncursesw);
    export   NCURSES_LIBS=$(pkg-config  --libs --static ncurses ncursesw);
    export   READLINE_CFLAGS=$(pkg-config --cflags --static readline)  ;
    export   READLINE_LIBS=$(pkg-config  --libs --static readline)  ;

    export   LIBPQ_CFLAGS=$(pkg-config  --cflags --static libpq)
    export   LIBPQ_LIBS=$(pkg-config    --libs   --static libpq)

    # export EXTRA_LIBS='<?= BROTLI_PREFIX ?>/lib/libbrotli.a <?= BROTLI_PREFIX ?>/lib/libbrotlicommon.a <?= BROTLI_PREFIX ?>/lib/libbrotlidec.a <?= BROTLI_PREFIX ?>/lib/libbrotlienc.a'

    # -lmcrypt
    # -lm  math.h 链接数学库， -lptread 链接线程库

    # macOS clang llvm 不支持  -static
    # export CFLAGS="-static"
    # export CFLAGS="-std=gnu11 -g -Wall -O3 -fPIE"
    # -std=gnu++ -fno-common -DPIC -static

    # package_names="${package_names}  libtiff-4 lcms2"
    # export CFLAGS="-Wno-error=implicit-function-declaration"
    CPPFLAGS="$(pkg-config  --cflags-only-I --static ${package_names} ) $CPPFLAGS"
    LDFLAGS="$(pkg-config   --libs-only-L   --static ${package_names} ) $LDFLAGS"
    LIBS="$(pkg-config      --libs-only-l   --static ${package_names} ) $LIBS"

    # macOS
    #  /Library/Developer/CommandLineTools/SDKs/MacOSX.sdk/usr/lib
    #  ll /Library/Developer/CommandLineTools/
    #  /Library/Developer/CommandLineTools/SDKs/MacOSX.sdk

    export_variables
    echo $LDFLAGS > <?= $this->getRootDir() ?>/ldflags.log
    echo $CPPFLAGS > <?= $this->getRootDir() ?>/cppflags.log
    echo $LIBS > <?= $this->getRootDir() ?>/libs.log

    ./configure --help
    ./configure --help | grep -e '--enable'
    ./configure --help | grep -e '--with'
    ./configure --help | grep -e '--disable'
    ./configure --help | grep -e '--without'
    ./configure --help | grep -e 'jit'


// libbrotlicommon.a 应该优先被链接
// 链接顺序问题
// Library order in static linking
# 参考  https://eli.thegreenplace.net/2013/07/09/library-order-in-static-linking
# 参考 https://bbs.huaweicloud.com/blogs/373470
# 参考   https://ftp.gnu.org/old-gnu/Manuals/ld-2.9.1/html_node/ld_3.html

//  -Wl,–whole-archive -Wl,–start-group a.o b.o c.o main.o -lf -ld -le -L./ -lc -Wl,–end-group -Wl,-no-whole-archive


# LIBS=" $LIBS -Wl,--whole-archive -Wl,--start-group "
# LIBS=" -Wl,--start-group  "

# export  LIBS=" $LIBS -Wl,--end-group -Wl,--no-whole-archive "
# export  LIBS=" $LIBS -Wl,--end-group   "
_____EO_____


    cd <?= $this->getWorkDir() . PHP_EOL ?>
}

make_build() {

    exit 0

   # export EXTRA_LDFLAGS="$(pkg-config   --libs-only-L   --static openssl libraw_r )"
   # export EXTRA_LDFLAGS_PROGRAM=""
   # EXTRA_LDFLAGS_PROGRAM='-all-static -fno-ident '


:<<'_____EO_____'
    export LDFLAGS="$LDFLAGS -all-static"
    make EXTRA_CFLAGS='<?= $this->extraCflags ?>' \
    EXTRA_LDFLAGS_PROGRAM=' <?= $this->extraLdflags ?> <?php foreach ($this->libraryList as $item) {
        if (!empty($item->ldflags)) {
            echo $item->ldflags;
            echo ' ';
        }
    } ?>'  -j <?= $this->maxJob ?> && echo ""
_____EO_____



}

make_build_old() {
    cd <?= $this->phpSrcDir . PHP_EOL ?>
    export_variables
    <?php if ($this->isLinux()) : ?>
    export LDFLAGS="$LDFLAGS  -static -all-static "
    <?php endif ;?>
    export LDFLAGS="$LDFLAGS   <?= $this->extraLdflags ?>"
    export EXTRA_CFLAGS='<?= $this->extraCflags ?>'
    <?php if(!empty($this->httpProxy)) : ?>
    <?= $this->getProxyConfig() . PHP_EOL ?>
    <?php endif ;?>
    make -j <?= $this->maxJob ?> ;
    <?php if(!empty($this->httpProxy)) : ?>
    unset HTTP_PROXY
    unset HTTPS_PROXY
    unset NO_PROXY
    <?php endif ;?>

<?php if ($this->isMacos()) : ?>
    xattr -cr <?= $this->phpSrcDir  ?>/sapi/cli/php
    otool -L <?= $this->phpSrcDir  ?>/sapi/cli/php
<?php else : ?>
    file <?= $this->phpSrcDir  ?>/sapi/cli/php
    readelf -h <?= $this->phpSrcDir  ?>/sapi/cli/php
<?php endif; ?>
    # make install
    mkdir -p <?= BUILD_PHP_INSTALL_PREFIX ?>/bin/
    cp -f <?= $this->phpSrcDir  ?>/sapi/cli/php <?= BUILD_PHP_INSTALL_PREFIX ?>/bin/
    echo "<?= $this->phpSrcDir  ?>/sapi/cli/php -v"
    <?= $this->phpSrcDir  ?>/sapi/cli/php -v
    echo "<?= BUILD_PHP_INSTALL_PREFIX ?>/bin/php -v"
    <?= BUILD_PHP_INSTALL_PREFIX ?>/bin/php -v

    # elfedit --output-osabi linux sapi/cli/php
}

make_archive() {
    set -x
    make_release_archive

    exit 0

    set -x
    cd <?= BUILD_PHP_INSTALL_PREFIX ?>/bin
    cp -f ${__PROJECT_DIR__}/bin/LICENSE .

    PHP_VERSION=$(./php -r "echo PHP_VERSION;")
    PHP_CLI_FILE_DEBUG=php-cli-v${PHP_VERSION}-<?=$this->getOsType()?>-<?=$this->getSystemArch()?>-debug.tar.xz
    tar -cJvf ${PHP_CLI_FILE_DEBUG} php LICENSE


    mkdir -p <?= BUILD_PHP_INSTALL_PREFIX ?>/bin/dist
    cp -f php           dist/
    cp -f LICENSE       dist/

    cd <?= BUILD_PHP_INSTALL_PREFIX ?>/bin/dist
    strip php
    PHP_CLI_FILE=php-cli-v${PHP_VERSION}-<?=$this->getOsType()?>-<?=$this->getSystemArch()?>.tar.xz
    tar -cJvf ${PHP_CLI_FILE} php LICENSE

    mv <?= BUILD_PHP_INSTALL_PREFIX ?>/bin/dist/${PHP_CLI_FILE}  ${__PROJECT_DIR__}/
    mv <?= BUILD_PHP_INSTALL_PREFIX ?>/bin/${PHP_CLI_FILE_DEBUG} ${__PROJECT_DIR__}/

    cd ${__PROJECT_DIR__}/
}

make_clean() {
    exit 0
    set -ex
    find . -name \*.gcno -o -name \*.gcda | grep -v "^\./thirdparty" | xargs rm -f
    find . -name \*.lo -o -name \*.o -o -name \*.dep | grep -v "^\./thirdparty" | xargs rm -f
    find . -name \*.la -o -name \*.a | grep -v "^\./thirdparty" | xargs rm -f
    find . -name \*.so | grep -v "^\./thirdparty" | xargs rm -f
    find . -name .libs -a -type d | grep -v "^./thirdparty" | xargs rm -rf
    rm -f libphp.la bin/swoole-cli     modules/* libs/*
    rm -f ext/opcache/jit/zend_jit_x86.c
    rm -f ext/opcache/jit/zend_jit_arm64.c
    rm -f ext/opcache/minilua
    rm -f libs.log ldflags.log cppflags.log
}

lib_pkg() {
    set +x
<?php foreach ($this->libraryList as $item) : ?>
    <?php if (!empty($item->pkgNames)) : ?>
        echo -e "[<?= $item->name ?>] pkg-config : \n<?= implode(' ', $item->pkgNames) ?>" ;
    <?php else :?>
        echo -e "[<?= $item->name ?>] pkg-config : \n"
    <?php endif ?>
    echo "==========================================================="
<?php endforeach; ?>
    exit 0
}

lib_dep_pkg() {
    set +x
    declare -A array_name
<?php foreach ($this->libraryList as $item) :?>
    <?php
    $pkgs = [];
    $this->getLibraryDependenciesByName($item->name, $pkgs);
    $pkgs = array_unique($pkgs);
    $res = implode(' ', $pkgs);
    ?>
    array_name[<?= $item->name ?>]="<?= $res?>"
<?php endforeach ;?>
    if test -n  "$1"  ;then
      echo -e "[$1] dependent pkgs :\n\n${array_name[$1]} \n"
    else
      for i in ${!array_name[@]}
      do
            echo -e "[${i}] dependent pkgs :\n\n${array_name[$i]} \n"
            echo "=================================================="
      done
    fi
    exit 0
}

lib_dep() {
    set +x
    declare -A array_name
<?php foreach ($this->libraryList as $item) :?>
    <?php
    $libs = [];
    $this->getLibraryDependentLibraryByName($item->name, $libs);
    $libs = array_unique($libs);
    $res = implode(' ', $libs);
    ?>
    array_name[<?= $item->name ?>]="<?= $res?>"
<?php endforeach ;?>

    for i in ${!array_name[@]}
    do
        echo -e "[${i}] dependent libraries :\n\n${array_name[$i]} \n"
        echo "=================================================="
    done
    exit 0
}
# 获得关联数组的所有元素值
# ${array_name[@]}
# ${array_name[*]}
# 获取关联数组的所有下标值
# ${!array_name[@]}
# ${!array_name[*]}
# 获得关联数组的长度
# ${#array_name[*]}
# ${#array_name[@]}


help() {
    echo "./make.sh docker-build [china|ustc|tuna]"
    echo "./make.sh docker-bash"
    echo "./make.sh docker-commit"
    echo "./make.sh docker-push"
    echo "./make.sh docker-stop"
    echo "./make.sh config"
    echo "./make.sh build"
    echo "./make.sh test"
    echo "./make.sh archive"
    echo "./make.sh all-library"
    echo "./make.sh list-library"
    echo "./make.sh list-extension"
    echo "./make.sh clean-all-library"
    echo "./make.sh clean-all-library-cached"
    echo "./make.sh sync"
    echo "./make.sh pkg-check"
    echo "./make.sh lib-pkg"
    echo "./make.sh lib-dep-pkg"
    echo "./make.sh lib-dep"
    echo "./make.sh variables"
    echo "./make.sh list-swoole-branch"
    echo "./make.sh switch-swoole-branch"
    echo "./make.sh [library-name]"
    echo  "./make.sh clean-[library-name]"
    echo  "./make.sh clean-[library-name]-cached"
    echo  "./make.sh clean"
}

if [ "$1" = "docker-build" ] ;then
    MIRROR=""
    if [ -n "$2" ]; then
        MIRROR=$2
    fi
    cd ${__PROJECT_DIR__}/sapi/docker
    docker build -t <?= Preprocessor::IMAGE_NAME ?>:<?= $this->getBaseImageTag() ?> -f <?= $this->getBaseImageDockerFile() ?>  . --build-arg="MIRROR=${MIRROR}"
    exit 0
elif [ "$1" = "docker-bash" ] ;then
    container=$(docker ps -a -f name=<?= Preprocessor::CONTAINER_NAME ?> | tail -n +2 2> /dev/null)
    base_image=$(docker images <?= Preprocessor::IMAGE_NAME ?>:<?= $this->getBaseImageTag() ?> | tail -n +2 2> /dev/null)
    image=$(docker images <?= Preprocessor::IMAGE_NAME ?>:<?= $this->getImageTag() ?> | tail -n +2 2> /dev/null)
    CONTAINER_STATE=$(docker inspect -f {{.State.Running}} <?= Preprocessor::CONTAINER_NAME ?> 2> /dev/null)
    if [[ "${CONTAINER_STATE}" != "true" ]]; then
        bash ./make.sh docker-stop
        container=''
    fi

    if [[ -z ${container} ]] ;then
        if [[ ! -z ${image} ]] ;then
            echo "swoole-cli-builder container does not exist, try to create with image[<?= Preprocessor::IMAGE_NAME ?>:<?= $this->getImageTag() ?>]"
            docker run -d --name <?= Preprocessor::CONTAINER_NAME ?> -v  ${__PROJECT_DIR__}:/work <?= Preprocessor::IMAGE_NAME ?>:<?= $this->getImageTag() ?> tini -- tail -f /dev/null
        elif [[ ! -z ${base_image} ]] ;then
            echo "swoole-cli-builder container does not exist, try to create with image[<?= Preprocessor::IMAGE_NAME ?>:<?= $this->getBaseImageTag() ?>]"
            docker run -d --name <?= Preprocessor::CONTAINER_NAME ?> -v  ${__PROJECT_DIR__}:/work  <?= Preprocessor::IMAGE_NAME ?>:<?= $this->getBaseImageTag() ?> tini -- tail -f /dev/null
        else
            echo "<?= Preprocessor::IMAGE_NAME ?>:<?= $this->getImageTag() ?> image does not exist, try to pull"
            echo "create container with <?= Preprocessor::IMAGE_NAME ?>:<?= $this->getImageTag() ?> image"
            docker run -d --name <?= Preprocessor::CONTAINER_NAME ?> -v  ${__PROJECT_DIR__}:/work  <?= Preprocessor::IMAGE_NAME ?>:<?= $this->getImageTag() ?> tini -- tail -f /dev/null
        fi
    fi
    docker exec -it <?= Preprocessor::CONTAINER_NAME ?> /bin/bash
    exit 0
elif [ "$1" = "docker-commit" ] ;then
    docker commit <?= Preprocessor::CONTAINER_NAME ?> <?= Preprocessor::IMAGE_NAME ?>:<?= $this->getImageTag() ?> && exit 0
elif [ "$1" = "docker-push" ] ;then
    docker push <?= Preprocessor::IMAGE_NAME ?>:<?= $this->getImageTag() ?> && exit 0
elif [ "$1" = "docker-stop" ] ;then
    {
        docker stop <?= Preprocessor::CONTAINER_NAME ?><?= PHP_EOL ?>
        docker rm <?= Preprocessor::CONTAINER_NAME ?><?= PHP_EOL ?>
    } || {
        echo $?
    }
elif [ "$1" = "all-library" ] ;then
    make_all_library
<?php foreach ($this->libraryList as $item) : ?>
elif [ "$1" = "<?=$item->name?>" ] ;then
    make_<?=$item->name?> && echo "[SUCCESS] make <?=$item->name?>"
    exit 0
elif [ "$1" = "clean-<?=$item->name?>" ] ;then
    clean_<?=$item->name?> && echo "[SUCCESS] make clean <?=$item->name?>"
    exit 0
elif [ "$1" = "clean-<?=$item->name?>-cached" ] ;then
    clean_<?=$item->name?>_cached && echo "[SUCCESS] clean <?=$item->name?> "
    exit 0
<?php endforeach; ?>
elif [ "$1" = "config" ] ;then
    make_config
elif [ "$1" = "build" ] ;then
    make_build
elif [ "$1" = "test" ] ;then
    <?= BUILD_PHP_INSTALL_PREFIX ?>/bin/php vendor/bin/phpunit
    exit 0
elif [ "$1" = "archive" ] ;then
    make_archive
    exit 0
elif [ "$1" = "clean-all-library" ] ;then
<?php foreach ($this->libraryList as $item) : ?>
    clean_<?=$item->name?> && echo "[SUCCESS] make clean [<?=$item->name?>]"
<?php endforeach; ?>
    exit 0
elif [ "$1" = "clean-all-library-cached" ] ;then
<?php foreach ($this->libraryList as $item) : ?>
    echo "rm <?= $this->getGlobalPrefix() ?>/<?= $item->name ?>/.completed"
    if [ -f <?=$this->getGlobalPrefix()?>/<?=$item->name?>/.completed ] ;then
        rm -f <?=$this->getGlobalPrefix()?>/<?=$item->name?>/.completed
    fi
<?php endforeach; ?>
    exit 0
elif [ "$1" = "diff-configure" ] ;then
    meld $SRC/configure.ac ./configure.ac
elif [ "$1" = "list-swoole-branch" ] ;then
    cd <?= $this->getRootDir() ?>/sapi/swoole
    git branch
elif [ "$1" = "switch-swoole-branch" ] ;then
    cd <?= $this->getRootDir() ?>/sapi/swoole
    SWOOLE_BRANCH=$2
    git checkout $SWOOLE_BRANCH
elif [ "$1" = "pkg-check" ] ;then
<?php foreach ($this->libraryList as $item) : ?>
    <?php if (!empty($item->pkgNames)) : ?>
    echo "[<?= $item->name ?>] pkg-config : <?= implode(' ', $item->pkgNames) ?>" ;
    pkg-config --cflags-only-I --static <?= implode(' ', $item->pkgNames) . PHP_EOL ?>
    pkg-config --libs-only-L   --static <?= implode(' ', $item->pkgNames) . PHP_EOL ?>
    pkg-config --libs-only-l   --static <?= implode(' ', $item->pkgNames) . PHP_EOL ?>
    <?php else :?>
    echo "[<?= $item->name ?>] pkg-config : no "
    <?php endif ?>
    echo "==========================================================="

<?php endforeach; ?>
    exit 0
elif [ "$1" = "lib-pkg" ] ;then
    lib_pkg
    exit 0
elif [ "$1" = "lib-dep-pkg" ] ;then
    lib_dep_pkg "$2"
    exit 0
elif [ "$1" = "lib-dep" ] ;then
    lib_dep "$2"
    exit 0
elif [ "$1" = "list-library" ] ;then
<?php foreach ($this->libraryList as $item) : ?>
    echo "<?= $item->name ?>"
<?php endforeach; ?>
    exit 0
elif [ "$1" = "list-extension" ] ;then
<?php foreach ($this->extensionList as $item) : ?>
    echo "<?= $item->name ?>"
<?php endforeach; ?>
    exit 0
elif [ "$1" = "clean" ] ;then
    make_clean
    exit 0
elif [ "$1" = "variables" ] ;then
    export_variables
    echo "===========================[CPPFLAGS]================================"
	echo $CPPFLAGS
    echo "===========================[CFLAGS]================================"
	echo $CFLAGS
    echo "===========================[LDFLAGS]================================"
	echo $LDFLAGS
    echo "===========================[LIBS]================================"
	echo $LIBS
elif [ "$1" = "sync" ] ;then
    PHP_CLI=$(which php)
    test -f ${__PROJECT_DIR__}/bin/runtime/php && PHP_CLI="${__PROJECT_DIR__}/bin/runtime/php -d curl.cainfo=${__PROJECT_DIR__}/bin/runtime/cacert.pem -d openssl.cafile=${__PROJECT_DIR__}/bin/runtime/cacert.pem"
    $PHP_CLI -v
    $PHP_CLI sync-source-code.php --action run
    exit 0
else
    help
fi

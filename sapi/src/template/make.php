<?php
/**
 * @var $this SwooleCli\Preprocessor
 */

use SwooleCli\Preprocessor;

?>
#!/usr/bin/env bash
shopt -s expand_aliases
__PROJECT_DIR__=$(cd "$(dirname "$0")"; pwd)
CLI_BUILD_TYPE=<?= $this->getBuildType() . PHP_EOL ?>
SRC=<?= $this->phpSrcDir . PHP_EOL ?>
ROOT=<?= $this->getRootDir() . PHP_EOL ?>
PREPARE_ARGS="<?= implode(' ', $this->getPrepareArgs())?>"
export LOGICAL_PROCESSORS=<?= trim($this->logicalProcessors). PHP_EOL ?>
export CMAKE_BUILD_PARALLEL_LEVEL=<?= $this->maxJob. PHP_EOL ?>
<?php if ($this->isMacos()) :?>
# 兼容 最低 macOS 版本
export MACOSX_DEPLOYMENT_TARGET=12.0
<?php endif; ?>
export CC=<?= $this->cCompiler . PHP_EOL ?>
export CXX=<?= $this->cppCompiler . PHP_EOL ?>
export LD=<?= $this->lld . PHP_EOL ?>

export PKG_CONFIG_PATH=<?= implode(':', $this->pkgConfigPaths) . PHP_EOL ?>
export PATH=<?= implode(':', $this->binPaths) . PHP_EOL ?>

OPTIONS="--disable-all \
    --enable-shared=no \
    --enable-static=yes \
    --without-valgrind \
    --with-config-file-path=<?= $this->getGlobalPrefix() ?>/etc/ \
    --with-config-file-scan-dir=<?= $this->getGlobalPrefix() ?>/etc/conf.d/ \
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

    <?php if ($item->enableInstallCached) : ?>
    if [ -f <?= $this->getGlobalPrefix() . '/' . $item->name ?>/.completed ] ;then
        echo "[<?= $item->name ?>]  library cached , skip.."
        return 0
    fi
    <?php endif; ?>

    # If the install directory exist, clean the install directory
    test -d  <?= $this->getGlobalPrefix() . '/' . $item->name ?>/ && rm -rf  <?= $this->getGlobalPrefix() . '/' . $item->name ?>/ ;

    <?php if (!$item->enableBuildCached) : ?>
    if [ -d <?=$this->getBuildDir()?>/<?=$item->name?>/ ]; then
        rm -rf <?=$this->getBuildDir()?>/<?=$item->name?>/
    fi
    <?php endif; ?>

    # If the source code directory does not exist, create a directory and decompress the source code archive
    if [ ! -d <?= $this->getBuildDir() ?>/<?= $item->name ?> ]; then
        mkdir -p <?= $this->getBuildDir() ?>/<?= $item->name . PHP_EOL ?>
        <?php if ($item->untarArchiveCommand == 'tar') : ?>
        tar --strip-components=1 -C <?= $this->getBuildDir() ?>/<?= $item->name ?> -xf <?= $this->workDir ?>/pool/lib/<?= $item->file ?>;
        <?php elseif ($item->untarArchiveCommand == 'unzip') :?>
        unzip -d  <?=$this->getBuildDir()?>/<?=$item->name?>   <?=$this->workDir?>/pool/lib/<?=$item->file ?>;
        <?php elseif ($item->untarArchiveCommand == 'tar-default') :?>
        tar  -C <?= $this->getBuildDir() ?>/<?= $item->name ?> -xf <?= $this->workDir ?>/pool/lib/<?= $item->file ?>;
        <?php endif ; ?>
        result_code=$?
        if [ $result_code -ne 0 ]; then
            echo "[<?=$item->name?>] [configure FAILURE]"
            rm -rf <?=$this->getBuildDir()?>/<?=$item->name?>/
            exit  $result_code
        fi
    fi

    cd <?=$this->getBuildDir()?>/<?=$item->name?>/

    <?php if ($item->enableBuildLibraryHttpProxy) : ?>
        <?= $this->getProxyConfig() . PHP_EOL ?>
        <?php if ($item->enableBuildLibraryGitProxy) :?>
            <?= $this->getGitProxyConfig() . PHP_EOL ?>
        <?php endif;?>
    <?php endif;?>

    # use build script replace  configure、make、make install
    <?php if (empty($item->buildScript)) : ?>
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

    <?php if ($item->enableInstallCached) : ?>
    if [ -d <?= $this->getGlobalPrefix() . '/'.  $item->name ?>/ ] ;then
        touch <?= $this->getGlobalPrefix() . '/'.  $item->name ?>/.completed
    fi
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
    make_<?=$item->name?> && echo "[SUCCESS] make <?=$item->name?>"
<?php endforeach; ?>
    return 0
}

before_configure_script() {
    cd <?= $this->getWorkDir() . PHP_EOL ?>
<?php foreach ($this->beforeConfigure as $name => $value) : ?>
    # ext <?= $name ?> hook
    <?= $value($this) . PHP_EOL ?>
<?php endforeach; ?>
    cd <?= $this->getWorkDir() . PHP_EOL ?>
    return 0
}

export_variables() {
    set -x
    CPPFLAGS=""
    CXXFLAGS=""
    CFLAGS=""
    LDFLAGS=""
    LIBS=""
<?php foreach ($this->variables as $name => $value) : ?>
    <?= key($value) ?>="<?= current($value) ?>"
<?php endforeach; ?>
    result_code=$?
    [[ $result_code -ne 0 ]] &&  echo " [ export_variables  FAILURE ]" && exit  $result_code;
    echo "export variables"
<?php foreach ($this->exportVariables as $value) : ?>
    export <?= key($value) ?>="<?= current($value) ?>"
<?php endforeach; ?>
    export CPPFLAGS=$(echo $CPPFLAGS | tr ' ' '\n' | sort | uniq | tr '\n' ' ')
    export CXXFLAGS=$(echo $CXXFLAGS | tr ' ' '\n' | sort | uniq | tr '\n' ' ')
    export CFLAGS=$(echo $CFLAGS | tr ' ' '\n' | sort | uniq | tr '\n' ' ')
    export LDFLAGS=$(echo $LDFLAGS | tr ' ' '\n' | sort | uniq | tr '\n' ' ')
    export LIBS=$(echo $LIBS | tr ' ' '\n' | sort | uniq | tr '\n' ' ')
<?php if ($this->hasExtension('opcache')):?>
    export CFLAGS="$CFLAGS -DPHP_ENABLE_OPCACHE"
    export CPPFLAGS="$CPPFLAGS -DPHP_ENABLE_OPCACHE"
<?php endif; ?>
<?php if ($this->isMacos() && !empty($this->frameworks)):?>
    # MACOS 链接 framework
    export LDFLAGS="$LDFLAGS <?php foreach($this->frameworks as $framework) { echo "-framework $framework "; } ?>"
<?php endif; ?>
<?php if ($this->isLinux()) : ?>
    # 手动指定依赖库链接顺序
    <?php if ($this->hasExtension('phpy')) : ?>
        export LIBS="$LIBS -lcrypto -lssl -lmpdec -lmpdec++ -lbz2 -llzma -lHacl_Hash_SHA2 -lb2 -lexpat -lxml2 -lform -lmenu  -ltic -lpanel -lncurses++ -lncurses "
    <?php endif; ?>
<?php endif; ?>
    result_code=$?
    [[ $result_code -ne 0 ]] &&  echo " [ export_variables  FAILURE ]" && exit  $result_code;
    set +x
    return 0
}

make_config() {
    before_configure_script
    cd <?= $this->getWorkDir() . PHP_EOL ?>
    test -f ./configure &&  rm ./configure
    ./buildconf --force
<?php if ($this->isLinux()) : ?>
    mv main/php_config.h.in /tmp/cnt
    echo -ne '#ifndef __PHP_CONFIG_H\n#define __PHP_CONFIG_H\n' > main/php_config.h.in
    cat /tmp/cnt >> main/php_config.h.in
    echo -ne '\n#endif\n' >> main/php_config.h.in
<?php endif; ?>

   ./configure --help
    export_variables
    export LDFLAGS="$LDFLAGS <?= $this->extraLdflags ?>"
    export EXTRA_CFLAGS='<?= $this->extraCflags ?>'
    echo $LDFLAGS > <?= $this->getWorkDir() ?>/ldflags.log
    echo $CPPFLAGS > <?= $this->getWorkDir() ?>/cppflags.log
    echo $LIBS > <?= $this->getWorkDir() ?>/libs.log

    ./configure $OPTIONS

<?php if ($this->isLinux()) : ?>
    sed -i.backup 's/-export-dynamic/-all-static/g' Makefile
<?php endif ; ?>
}

make_build() {
    cd <?= $this->getWorkDir() . PHP_EOL ?>
    export_variables
    <?php if ($this->isLinux()) : ?>
    export CFLAGS="$CFLAGS  "
    export LDFLAGS="$LDFLAGS  -static -all-static"
    <?php if ($this->getInputOption('with-static-pie')) : ?>
    export CFLAGS="$CFLAGS  -fPIE"
    export LDFLAGS="$LDFLAGS -static-pie"
    <?php endif ; ?>
    <?php endif ; ?>
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
    xattr -cr <?= $this->getWorkDir() ?>/bin/swoole-cli
    otool -L <?= $this->getWorkDir() ?>/bin/swoole-cli
<?php else : ?>
    { ldd  <?= $this->getWorkDir() ?>/bin/swoole-cli ; } || { echo $? ; }
    file <?= $this->getWorkDir() ?>/bin/swoole-cli
    readelf -h <?= $this->getWorkDir() ?>/bin/swoole-cli
    { readelf -l <?= $this->getWorkDir() ?>/bin/swoole-cli ; } || { echo $? ; }
    { objdump -p <?= $this->getWorkDir() ?>/bin/swoole-cli ; } || { echo $? ; }
<?php endif; ?>
}

make_archive() {
    set -x
    cd ${__PROJECT_DIR__}/bin/
    SWOOLE_VERSION=$(./swoole-cli -r "echo SWOOLE_VERSION;")

    SWOOLE_CLI_FILE_DEBUG=swoole-cli-v${SWOOLE_VERSION}-<?=$this->getOsType()?>-<?=$this->getSystemArch()?>-debug.tar.xz
    tar -cJvf ${SWOOLE_CLI_FILE_DEBUG} swoole-cli LICENSE pack-sfx.php

    HASH=$(sha256sum ${SWOOLE_CLI_FILE_DEBUG} | awk '{print $1}')
    echo " ${SWOOLE_CLI_FILE_DEBUG} sha256sum: ${HASH} "
    echo -n ${HASH} > ${SWOOLE_CLI_FILE_DEBUG}.sha256sum


    mkdir -p ${__PROJECT_DIR__}/bin/dist
    cp -f swoole-cli    dist/
    cp -f LICENSE       dist/
    cp -f pack-sfx.php  dist/

    cd ${__PROJECT_DIR__}/bin/dist/
    SWOOLE_CLI_FILE=swoole-cli-v${SWOOLE_VERSION}-<?=$this->getOsType()?>-<?=$this->getSystemArch()?>.tar.xz
    strip swoole-cli
    tar -cJvf ${SWOOLE_CLI_FILE} swoole-cli LICENSE pack-sfx.php

    HASH=$(sha256sum ${SWOOLE_CLI_FILE} | awk '{print $1}')
    echo " ${SWOOLE_CLI_FILE} sha256sum: ${HASH} "
    echo -n ${HASH} > ${SWOOLE_CLI_FILE}.sha256sum

    cd ${__PROJECT_DIR__}/
    mv bin/dist/${SWOOLE_CLI_FILE}  ${__PROJECT_DIR__}/
    mv bin/dist/${SWOOLE_CLI_FILE}.sha256sum  ${__PROJECT_DIR__}/
    mv bin/${SWOOLE_CLI_FILE_DEBUG} ${__PROJECT_DIR__}/
    mv bin/${SWOOLE_CLI_FILE_DEBUG}.sha256sum ${__PROJECT_DIR__}/

    cd ${__PROJECT_DIR__}/
}

make_clean() {
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
    $pkgs=[];
    $this->getLibraryDependenciesByName($item->name, $pkgs);
    $pkgs = array_unique($pkgs);
    $res=implode(' ', $pkgs);
    ?>
    array_name[<?= $item->name ?>]="<?= $res?>"
<?php endforeach ;?>
    if test -n  "$1"  ;then
      echo -e "[$1] dependent pkgs :\n\n${array_name[$1]} \n"
    else
      for i in "${!array_name[@]}"
      do
            echo -e "[${i}] dependent pkgs :\n\n${array_name[$i]} \n"
            echo "=================================================="
      done
    fi
    exit 0
}

make_swoole_cli_with_linux_gcc() {
    if [ ! -f bin/swoole-cli ] ;then
        ./buildconf --force
        ./sapi/scripts/build-swoole-cli-with-linux-gcc.sh
    fi
}

make_nfpm_pkg() {
    make_swoole_cli_with_linux_gcc
    ./bin/swoole-cli sapi/scripts/copy-depend-libs.php
    patchelf --force-rpath --set-rpath '/usr/local/swoole-cli/lib' bin/swoole-cli
    NFPM_PKG_FILENAME=swoole-cli-<?=$this->getSwooleVersion()?>-linux-<?=$this->getSystemArch()?>-glibc
    nfpm pkg --config nfpm-pkg.yaml --target "${NFPM_PKG_FILENAME}.rpm"
    nfpm pkg --config nfpm-pkg.yaml --target "${NFPM_PKG_FILENAME}.deb"
    return 0
}

help() {
    echo "./make.sh docker-build [ china | ustc | tuna ]"
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
    echo "./make.sh variables"
    echo "./make.sh list-swoole-branch"
    echo "./make.sh switch-swoole-branch"
    echo "./make.sh [library-name]"
    echo "./make.sh nfpm-pkg"
    echo  "./make.sh clean-[library-name]"
    echo  "./make.sh clean-[library-name]-cached"
    echo  "./make.sh clean"
}

if [ "$1" = "docker-build" ] ;then
    MIRROR=""
    CONTAINER_BASE_IMAGE='docker.io/library/alpine:3.18'
    PLATFORM=''
    ARCH=$(uname -m)
    case $ARCH in
    'x86_64')
      PLATFORM='linux/amd64'
      ;;
    'aarch64')
      PLATFORM='linux/arm64'
      ;;
    esac
    cd ${__PROJECT_DIR__}/sapi/docker
    echo "MIRROR=${MIRROR}"
    echo "BASE_IMAGE=${CONTAINER_BASE_IMAGE}"
    docker build --no-cache -t <?= Preprocessor::IMAGE_NAME ?>:<?= $this->getBaseImageTag() ?> -f Dockerfile  . --build-arg="MIRROR=${MIRROR}" --platform=${PLATFORM} --build-arg="BASE_IMAGE=${CONTAINER_BASE_IMAGE}"
    exit 0
elif [ "$1" = "docker-bash" ] ;then
    container=$(docker ps -a -f name=<?= Preprocessor::CONTAINER_NAME ?> | tail -n +2 2> /dev/null)
    base_image=$(docker images <?= Preprocessor::IMAGE_NAME ?>:<?= $this->getBaseImageTag() ?> | tail -n +2 2> /dev/null)
    image=$(docker images <?= Preprocessor::IMAGE_NAME ?>:<?= $this->getImageTag() ?> | tail -n +2 2> /dev/null)
    CONTAINER_STATE=$(docker inspect -f "{{.State.Running}}" <?= Preprocessor::CONTAINER_NAME ?> 2> /dev/null)
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
            # check container image exists
            # curl -fsSlL --head https://hub.docker.com/v2/repositories/$1/tags/$2/ > /dev/null && echo "exist" || echo "not exists"
            # curl -fsSlL --head https://hub.docker.com/v2/repositories/<?= Preprocessor::IMAGE_NAME ?>/tags/<?= $this->getImageTag() ?>/ > /dev/null && echo "container image exist" || echo "container image  not exists"
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
    ./bin/swoole-cli vendor/bin/phpunit
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
elif [ "$1" = "nfpm-pkg" ] ;then
    make_nfpm_pkg
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
    test -f ${__PROJECT_DIR__}/runtime/php/php && PHP_CLI="${__PROJECT_DIR__}/runtime/php/php -d curl.cainfo=${__PROJECT_DIR__}/runtime/php/cacert.pem -d openssl.cafile=${__PROJECT_DIR__}/runtime/php/cacert.pem"
    $PHP_CLI -v
    $PHP_CLI sync-source-code.php --action run
    exit 0
else
    help
fi

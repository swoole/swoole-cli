<?php
/**
 * @var $this SwooleCli\Preprocessor
 */
use SwooleCli\Preprocessor;
?>
SRC=<?= $this->phpSrcDir . PHP_EOL ?>
ROOT=<?= $this->getRootDir() . PHP_EOL ?>
export CC=<?= $this->cCompiler . PHP_EOL ?>
export CXX=<?= $this->cppCompiler . PHP_EOL ?>
export LD=<?= $this->lld . PHP_EOL ?>
export PKG_CONFIG_PATH=<?= implode(':', $this->pkgConfigPaths) . PHP_EOL ?>
export PATH=<?= implode(':', $this->binPaths) . PHP_EOL ?>
OPTIONS="--disable-all \
--enable-shared=no \
--enable-static=yes \
<?php foreach ($this->extensionList as $item) : ?>
<?=$item->options?> \
<?php endforeach; ?>
<?=$this->extraOptions?>
"

<?php foreach ($this->libraryList as $item) : ?>
make_<?=$item->name?>() {
    echo "build <?=$item->name?>"

    # If the source code directory does not exist, create a directory and decompress the source code archive
    if [ ! -d <?= $this->getBuildDir() ?>/<?= $item->name ?> ]; then
        mkdir -p <?= $this->getBuildDir() ?>/<?= $item->name . PHP_EOL ?>
        tar --strip-components=1 -C <?= $this->getBuildDir() ?>/<?= $item->name ?> -xf <?= $this->workDir ?>/pool/lib/<?= $item->file . PHP_EOL ?>
        result_code=$?
        if [ $result_code -ne 0 ]; then
            echo "[<?=$item->name?>] [configure FAILURE]"
            rm -rf <?=$this->getBuildDir()?>/<?=$item->name?>/
            exit  $result_code
        fi
    fi

    if [ -f <?=$this->getBuildDir()?>/<?=$item->name?>/.completed ]; then
        echo "[<?=$item->name?>] compiled, skip.."
        cd <?= $this->workDir ?>/
        return 0
    fi

    cd <?=$this->getBuildDir()?>/<?=$item->name?>/

    # use build script replace  configure、make、make install
<?php if(empty($item->buildScript)): ?>
    # configure
<?php if (!empty($item->configure)): ?>
cat <<'__EOF__'
    <?= $item->configure . PHP_EOL ?>
__EOF__
    <?=$item->configure . PHP_EOL ?>
    result_code=$?
    [[ $result_code -ne 0 ]] &&  echo "[<?=$item->name?>] [configure FAILURE]" && exit  $result_code;
<?php endif; ?>

    # make
    make -j <?= $this->maxJob ?> <?= $item->makeOptions . PHP_EOL ?>
    result_code=$?
    [[ $result_code -ne 0 ]] &&  echo "[<?=$item->name?>] [make FAILURE]" && exit  $result_code;

    # before make install
<?php if ($item->beforeInstallScript): ?>
    <?=$item->beforeInstallScript . PHP_EOL ?>
    result_code=$?
    [[ $result_code -ne 0 ]] &&  echo "[<?=$item->name?>] [ before make install script FAILURE]" && exit  $result_code;
<?php endif; ?>

    # make install
<?php if ($item->makeInstallCommand): ?>
    make <?= $item->makeInstallCommand ?> <?= $item->makeInstallOptions ?> <?= PHP_EOL ?>
    result_code=$?
    [[ $result_code -ne 0 ]] &&  echo "[<?=$item->name?>] [make install FAILURE]" && exit  $result_code;
<?php endif; ?>
<?php else: ?>
    cat <<'__EOF__'
    <?= $item->buildScript . PHP_EOL ?>
__EOF__
    <?= $item->buildScript . PHP_EOL ?>
    result_code=$?
    [[ $result_code -ne 0 ]] &&  echo "[<?=$item->name?>] [build script FAILURE]" && exit  $result_code;
<?php endif; ?>

    # after make install
<?php if ($item->afterInstallScript): ?>
    <?=$item->afterInstallScript . PHP_EOL ?>
    result_code=$?
    [[ $result_code -ne 0 ]] &&  echo "[<?=$item->name?>] [ after make  install script FAILURE]" && exit  $result_code;
<?php endif; ?>

    touch <?=$this->getBuildDir()?>/<?=$item->name?>/.completed

    cd <?= $this->workDir . PHP_EOL ?>
    return 0
}

clean_<?=$item->name?>() {
    cd <?=$this->getBuildDir()?> && echo "clean <?=$item->name?>"
    cd <?=$this->getBuildDir()?>/<?= $item->name ?> && make clean
    rm -f <?=$this->getBuildDir()?>/<?=$item->name?>/.completed
    cd <?= $this->workDir . PHP_EOL ?>
}

clean_<?=$item->name?>_cached() {
    echo "clean <?=$item->name?> [cached]"
    rm <?=$this->getBuildDir()?>/<?=$item->name?>/.completed
}

<?php echo str_repeat(PHP_EOL, 1);?>
<?php endforeach; ?>

make_all_library() {
<?php foreach ($this->libraryList as $item) : ?>
    make_<?=$item->name?> && echo "[SUCCESS] make <?=$item->name?>"
<?php endforeach; ?>
    return 0
}

make_config() {
    cd <?= $this->getWorkDir() . PHP_EOL ?>
    set -exu

<?php if (isset($this->extensionDependPkgNameMap['intl']) || isset($this->extensionDependPkgNameMap['mongodb'])) :?>
    export   ICU_CFLAGS=$(pkg-config  --cflags --static icu-i18n  icu-io   icu-uc)
    export   ICU_LIBS=$(pkg-config    --libs   --static icu-i18n  icu-io   icu-uc)
<?php endif; ?>

<?php if (isset($this->extensionDependPkgNameMap['xsl']))  :?>
    export   XSL_CFLAGS=$(pkg-config    --cflags --static libxslt)
    export   XSL_LIBS=$(pkg-config      --libs   --static libxslt)
    export   EXSLT_CFLAGS=$(pkg-config  --cflags --static libexslt)
    export   EXSLT_LIBS=$(pkg-config    --libs   --static libexslt)
<?php endif; ?>

<?php if (isset($this->extensionDependPkgNameMap['mbstring']))  :?>
    export   ONIG_CFLAGS=$(pkg-config --cflags --static oniguruma)
    export   ONIG_LIBS=$(pkg-config   --libs   --static oniguruma)
<?php endif; ?>

<?php if (isset($this->extensionDependPkgNameMap['sodium']))  :?>
    export   LIBSODIUM_CFLAGS=$(pkg-config --cflags --static libsodium)
    export   LIBSODIUM_LIBS=$(pkg-config   --libs   --static libsodium)
<?php endif; ?>

<?php if (isset($this->extensionDependPkgNameMap['zip']))  :?>
    export   LIBZIP_CFLAGS=$(pkg-config --cflags --static libzip)
    export   LIBZIP_LIBS=$(pkg-config   --libs   --static libzip)
<?php endif; ?>

<?php if (isset($this->extensionDependPkgNameMap['mongodb']))  :?>
    export   PHP_MONGODB_SSL_CFLAGS=$(pkg-config --cflags --static libcrypto libssl  openssl)
    export   PHP_MONGODB_SSL_LIBS=$(pkg-config   --libs   --static libcrypto libssl  openssl)
    export   PHP_MONGODB_ICU_CFLAGS=$(pkg-config --cflags --static icu-i18n  icu-io  icu-uc)
    export   PHP_MONGODB_ICU_LIBS=$(pkg-config   --libs   --static icu-i18n  icu-io  icu-uc)
<?php endif; ?>

    package_names=''
<?php

    foreach ($this->extensionDependPkgNameMap as $extensionName => $package) {
        if (empty($package)) {
            continue;
        }
        echo "    # {$extensionName} : ";
        echo PHP_EOL;
        echo '    # package_names="${package_names} ' . implode(' ', $package) . '" ';
        echo PHP_EOL;
    }

?>
    package_names="${package_names}  <?= implode(' ', $this->extensionDependPkgNameList) ?> "
    imagemagick=""
<?php if (isset($this->extensionDependPkgNameMap['imagick'])) :?>
    imagemagick="<?= $this->getPkgNameByLibraryName('imagemagick') ?>"
<?php endif; ?>

<?php if ($this->getOsType() == 'linux') : ?>
    package_names=" ${package_names} ${imagemagick}"
<?php endif; ?>

    CPPFLAGS=""
    LDFLAGS=""
    LIBS=""
<?php if (isset($this->extensionDependPkgNameMap['iconv'])) : ?>
    CPPFLAGS="$CPPFLAGS -I<?= ICONV_PREFIX ?>/include "
    LDFLAGS="$LDFLAGS   -L<?= ICONV_PREFIX ?>/lib"
    LIBS="$LIBS -liconv"
<?php endif; ?>

<?php if (isset($this->extensionDependPkgNameMap['bz2'])) : ?>
    CPPFLAGS="$CPPFLAGS -I<?= BZIP2_PREFIX ?>/include"
    LDFLAGS="$LDFLAGS   -L<?= BZIP2_PREFIX ?>/lib"
    LIBS="$LIBS -lbz2"
<?php endif; ?>

<?php if (!empty($this->configureVarables)) :?>
    <?= $this->configureVarables ?>" ${LDFLAGS}"
<?php endif; ?>

    if <?= !empty($this->extensionDependPkgNameList) ? 'true' : 'false' ; ?> ;then
        CPPFLAGS="$(pkg-config  --cflags-only-I --static ${package_names} ) $CPPFLAGS"
        LDFLAGS="$(pkg-config   --libs-only-L   --static ${package_names} ) $LDFLAGS"
        LIBS="$(pkg-config      --libs-only-l   --static ${package_names} ) $LIBS"
    fi

    if [ -n  "$CPPFLAGS" ] ;then

<?php if ($this->getOsType() == 'linux') : ?>
        LIBS="$LIBS -lstdc++"
<?php endif; ?>
<?php if ($this->getOsType() == 'macos') : ?>
        LIBS="$LIBS -lc++"
<?php endif; ?>

<?php if ($this->getOsType() == 'linux') : ?>
        export  CPPFLAGS="$CPPFLAGS "
        export  LDFLAGS="$LDFLAGS "
        export  LIBS="$LIBS  "
<?php endif; ?>

<?php if ($this->getOsType() == 'macos') : ?>
        export CPPFLAGS="$CPPFLAGS "
        export EXTRA_LDFLAGS="$LDFLAGS "
        export EXTRA_LIBS="$LIBS "

<?php if (isset($this->extensionDependPkgNameMap['imagick'])) :?>
        IMAGICK_LDFLAGS=$(pkg-config   --cflags-only-I   --static $imagemagick )
        IMAGICK_LIBS=$(pkg-config      --libs-only-l     --static $imagemagick )
<?php endif; ?>

<?php endif; ?>

    fi

:<<'EOF'
    # 更多配置
    export EXTRA_INCLUDES=
    export EXTRA_CFLAGS
    export EXTRA_LDFLAGS=
    export EXTRA_LDFLAGS_PROGRAM=
    export EXTRA_LIBS=
    export ZEND_EXTRA_LIBS=
EOF

    test -f ./configure &&  rm ./configure
    ./buildconf --force
<?php if ($this->osType !== 'macos') : ?>
    mv main/php_config.h.in /tmp/cnt
    echo -ne '#ifndef __PHP_CONFIG_H\n#define __PHP_CONFIG_H\n' > main/php_config.h.in
    cat /tmp/cnt >> main/php_config.h.in
    echo -ne '\n#endif\n' >> main/php_config.h.in
<?php endif; ?>
    echo $OPTIONS
    echo $PKG_CONFIG_PATH

    ./configure $OPTIONS
}

make_build() {
    cd <?= $this->getWorkDir() . PHP_EOL ?>
    make EXTRA_CFLAGS='<?= $this->extraCflags ?>' \
    EXTRA_LDFLAGS_PROGRAM='-all-static -fno-ident <?= $this->extraLdflags ?> <?php foreach ($this->libraryList as $item) {
        if (!empty($item->ldflags)) {
            echo $item->ldflags;
            echo ' ';
        }
    } ?>'  -j <?= $this->maxJob ?> && echo ""
}

help() {
    echo "./make.sh docker-build"
    echo "./make.sh docker-bash"
    echo "./make.sh docker-commit"
    echo "./make.sh docker-push"
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
    echo "./make.sh list-swoole-branch"
    echo "./make.sh switch-swoole-branch"
    echo "./make.sh [library-name]"
    echo  "./make.sh clean-[library-name]"
    echo  "./make.sh clean-[library-name]-cached"
    echo  "./make.sh clean"
}

if [ "$1" = "docker-build" ] ;then
    cd <?=$this->getRootDir()?>/sapi
    docker build -t <?= Preprocessor::IMAGE_NAME ?>:<?= $this->getBaseImageTag() ?> -f <?= $this->getBaseImageDockerFile() ?>  .
    exit 0
elif [ "$1" = "docker-bash" ] ;then
    container=$(docker ps -a -f name=<?= Preprocessor::CONTAINER_NAME ?> | tail -n +2 2> /dev/null)
    base_image=$(docker images <?= Preprocessor::IMAGE_NAME ?>:<?= $this->getBaseImageTag() ?> | tail -n +2 2> /dev/null)
    image=$(docker images <?= Preprocessor::IMAGE_NAME ?>:<?= $this->getImageTag() ?> | tail -n +2 2> /dev/null)

    if [[ -z ${container} ]] ;then
        if [[ ! -z ${image} ]] ;then
            echo "swoole-cli-builder container does not exist, try to create with image[<?= Preprocessor::IMAGE_NAME ?>:<?= $this->getImageTag() ?>]"
            docker run -it --name <?= Preprocessor::CONTAINER_NAME ?> -v ${ROOT}:<?=$this->getWorkDir()?> <?= Preprocessor::IMAGE_NAME ?>:<?= $this->getImageTag() ?> /bin/bash
        elif [[ ! -z ${base_image} ]] ;then
            echo "swoole-cli-builder container does not exist, try to create with image[<?= Preprocessor::IMAGE_NAME ?>:<?= $this->getBaseImageTag() ?>]"
            docker run -it --name <?= Preprocessor::CONTAINER_NAME ?> -v ${ROOT}:<?=$this->getWorkDir()?> <?= Preprocessor::IMAGE_NAME ?>:<?= $this->getBaseImageTag() ?> /bin/bash
        else
            echo "<?= Preprocessor::IMAGE_NAME ?>:<?= $this->getImageTag() ?> image does not exist, try to pull"
            echo "create container with <?= Preprocessor::IMAGE_NAME ?>:<?= $this->getImageTag() ?> image"
            docker run -it --name <?= Preprocessor::CONTAINER_NAME ?> -v ${ROOT}:<?=$this->getWorkDir()?> <?= Preprocessor::IMAGE_NAME ?>:<?= $this->getImageTag() ?> /bin/bash
        fi
    else
        if [[ "${container}" =~ "Exited" ]]; then
            docker start <?= Preprocessor::CONTAINER_NAME ?> ;
        fi
        docker exec -it <?= Preprocessor::CONTAINER_NAME ?> /bin/bash
    fi
    exit 0
elif [ "$1" = "docker-commit" ] ;then
    docker commit <?= Preprocessor::CONTAINER_NAME ?> <?= Preprocessor::IMAGE_NAME ?>:<?= $this->getImageTag() ?> && exit 0
elif [ "$1" = "docker-commit" ] ;then
    docker push <?= Preprocessor::IMAGE_NAME ?>:<?= $this->getImageTag() ?> && exit 0
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
    cd bin
    SWOOLE_VERSION=$(./swoole-cli -r "echo SWOOLE_VERSION;")
    SWOOLE_CLI_FILE=swoole-cli-v${SWOOLE_VERSION}-<?=$this->getOsType()?>-<?=$this->getSystemArch()?>.tar.xz
    strip swoole-cli
    tar -cJvf ${SWOOLE_CLI_FILE} swoole-cli LICENSE
    mv ${SWOOLE_CLI_FILE} ../
    cd -
elif [ "$1" = "clean-all-library" ] ;then
<?php foreach ($this->libraryList as $item) : ?>
    clean_<?=$item->name?> && echo "[SUCCESS] make clean [<?=$item->name?>]"
<?php endforeach; ?>
    exit 0
elif [ "$1" = "clean-all-library-cached" ] ;then
<?php foreach ($this->libraryList as $item) : ?>
    echo "rm <?= $this->getBuildDir() ?>/<?= $item->name ?>/.completed"
    rm <?= $this->getBuildDir() ?>/<?= $item->name ?>/.completed
<?php endforeach; ?>
    exit 0
elif [ "$1" = "diff-configure" ] ;then
    meld $SRC/configure.ac ./configure.ac
elif [ "$1" = "list-swoole-branch" ] ;then
    cd <?= $this->getRootDir() ?>/ext/swoole
    git branch
elif [ "$1" = "switch-swoole-branch" ] ;then
    cd <?= $this->getRootDir() ?>/ext/swoole
    SWOOLE_BRANCH=$2
    git checkout $SWOOLE_BRANCH
elif [ "$1" = "pkg-check" ] ;then
<?php foreach ($this->libraryList as $item) : ?>
    echo "[<?= $item->name ?>]"
<?php if(!empty($item->pkgName)) :?>
    pkg-config --libs <?= $item->pkgName . PHP_EOL ?>
<?php else :?>
    echo "no PKG_CONFIG !"
<?php endif ?>
    echo "==========================================================="
<?php endforeach; ?>
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
    find . -path "./thirdparty" -name \*.gcno -o -name \*.gcda | xargs rm -f
    find . -path "./thirdparty" -name \*.lo -o -name \*.o -o -name \*.dep | xargs rm -f
    find . -path "./thirdparty" -name \*.la -o -name \*.a | xargs rm -f
    find . -path "./thirdparty" -name \*.so | xargs rm -f
    find . -path "./thirdparty" -name .libs -a -type d | xargs rm -rf
    rm -f libphp.la bin/swoole-cli     modules/* libs/*
    rm -f ext/opcache/jit/zend_jit_x86.c
    rm -f ext/opcache/jit/zend_jit_arm64.c
    rm -f ext/opcache/minilua
elif [ "$1" = "sync" ] ;then
  echo "sync"
  # ZendVM
  cp -r $SRC/Zend ./
  # Extension
  cp -r $SRC/ext/bcmath/ ./ext
  cp -r $SRC/ext/bz2/ ./ext
  cp -r $SRC/ext/calendar/ ./ext
  cp -r $SRC/ext/ctype/ ./ext
  cp -r $SRC/ext/curl/ ./ext
  cp -r $SRC/ext/date/ ./ext
  cp -r $SRC/ext/dom/ ./ext
  cp -r $SRC/ext/exif/ ./ext
  cp -r $SRC/ext/fileinfo/ ./ext
  cp -r $SRC/ext/filter/ ./ext
  cp -r $SRC/ext/gd/ ./ext
  cp -r $SRC/ext/gettext/ ./ext
  cp -r $SRC/ext/gmp/ ./ext
  cp -r $SRC/ext/hash/ ./ext
  cp -r $SRC/ext/iconv/ ./ext
  cp -r $SRC/ext/intl/ ./ext
  cp -r $SRC/ext/json/ ./ext
  cp -r $SRC/ext/libxml/ ./ext
  cp -r $SRC/ext/mbstring/ ./ext
  cp -r $SRC/ext/mysqli/ ./ext
  cp -r $SRC/ext/mysqlnd/ ./ext
  cp -r $SRC/ext/opcache/ ./ext
  sed -i 's/ext_shared=yes/ext_shared=no/g' ext/opcache/config.m4 && sed -i 's/shared,,/$ext_shared,,/g' ext/opcache/config.m4
  echo -e '#include "php.h"\n\nextern zend_module_entry opcache_module_entry;\n#define phpext_opcache_ptr  &opcache_module_entry\n' > ext/opcache/php_opcache.h
  cp -r $SRC/ext/openssl/ ./ext
  cp -r $SRC/ext/pcntl/ ./ext
  cp -r $SRC/ext/pcre/ ./ext
  cp -r $SRC/ext/pdo/ ./ext
  cp -r $SRC/ext/pdo_mysql/ ./ext
  cp -r $SRC/ext/pdo_sqlite/ ./ext
  cp -r $SRC/ext/phar/ ./ext
  cp -r $SRC/ext/posix/ ./ext
  cp -r $SRC/ext/readline/ ./ext
  cp -r $SRC/ext/reflection/ ./ext
  cp -r $SRC/ext/session/ ./ext
  cp -r $SRC/ext/simplexml/ ./ext
  cp -r $SRC/ext/soap/ ./ext
  cp -r $SRC/ext/sockets/ ./ext
  cp -r $SRC/ext/sodium/ ./ext
  cp -r $SRC/ext/spl/ ./ext
  cp -r $SRC/ext/sqlite3/ ./ext
  cp -r $SRC/ext/standard/ ./ext
  cp -r $SRC/ext/sysvshm/ ./ext
  cp -r $SRC/ext/tokenizer/ ./ext
  cp -r $SRC/ext/xml/ ./ext
  cp -r $SRC/ext/xmlreader/ ./ext
  cp -r $SRC/ext/xmlwriter/ ./ext
  cp -r $SRC/ext/xsl/ ./ext
  cp -r $SRC/ext/zip/ ./ext
  cp -r $SRC/ext/zlib/ ./ext
  # main
  cp -r $SRC/main ./
  sed -i 's/\/\* start Zend extensions \*\//\/\* start Zend extensions \*\/\n\textern zend_extension zend_extension_entry;\n\tzend_register_extension(\&zend_extension_entry, NULL);/g' main/main.c
  # build
  cp -r $SRC/build ./
  # TSRM
  cp -r ./TSRM/TSRM.h main/TSRM.h
  cp -r $SRC/configure.ac ./
  # fpm
  cp -r $SRC/sapi/fpm/fpm ./sapi/cli
  exit 0
else
    help
fi


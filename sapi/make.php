<?php
/**
 * @var $this SwooleCli\Preprocessor
 */
?>
SRC=<?= $this->phpSrcDir . PHP_EOL ?>
ROOT=$(pwd)
export CC=clang
export CXX=clang++
export LD=ld.lld
export PKG_CONFIG_PATH=<?= implode(':', $this->pkgConfigPaths) . PHP_EOL ?>
OPTIONS="--disable-all \
<?php foreach ($this->extensionList as $item) : ?>
<?=$item->options?> \
<?php endforeach; ?>
"

<?php foreach ($this->libraryList as $item) : ?>
make_<?=$item->name?>() {
    cd <?=$this->workDir?>/thirdparty
    echo "build <?=$item->name?>"
    mkdir -p <?=$this->workDir?>/thirdparty/<?=$item->name?> && \
    tar --strip-components=1 -C <?=$this->workDir?>/thirdparty/<?=$item->name?> -xf <?=$this->libraryDir?>/<?=$item->file?>  && \
    cd <?=$item->name?> && \
    echo  "<?=$item->configure?>"
    <?php if (!empty($item->configure)): ?>
    <?=$item->configure?> && \
    <?php endif; ?>
    make -j <?=$this->maxJob?>  <?=$item->makeOptions?> && \
    make install <?=$item->makeInstallOptions?> && \
    <?php if ($item->clearDylib): ?>
    find <?=$item->prefix?> -name \*.dylib | xargs rm -f && \
    <?php endif; ?>
    cd -
}

clean_<?=$item->name?>() {
    cd <?=$this->workDir?>/thirdparty
    echo "clean <?=$item->name?>"
    cd <?=$this->workDir?>/thirdparty/<?=$item->name?> && make clean
    cd -
}
<?php echo str_repeat(PHP_EOL, 1);?>
<?php endforeach; ?>

make_all_library() {
<?php foreach ($this->libraryList as $item) : ?>
    make_<?=$item->name?> && echo "[SUCCESS] make <?=$item->name?>"
<?php endforeach; ?>
}

config_php() {
    rm ./configure
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

make_php() {
    make EXTRA_CFLAGS='-fno-ident -Xcompiler -march=nehalem -Xcompiler -mtune=haswell -Os' \
    EXTRA_LDFLAGS_PROGRAM='-all-static -fno-ident <?=$this->extraLdflags?> <?php foreach ($this->libraryList as $item) {
        if (!empty($item->ldflags)) {
            echo $item->ldflags;
            echo ' ';
        }
    } ?>'  -j <?=$this->maxJob?> && echo ""
}

help() {
    echo "./make.sh docker-bash"
    echo "./make.sh config"
    echo "./make.sh build"
    echo "./make.sh archive"
    echo "./make.sh all-library"
    echo "./make.sh clean-all-library"
    echo "./make.sh sync"
}

if [ "$1" = "docker-build" ] ;then
  sudo docker build -t phpswoole/swoole_cli_os:<?= $this->dockerVersion ?> .
elif [ "$1" = "docker-bash" ] ;then
    sudo docker run -it -v $ROOT:<?=$this->workDir?> -v <?= $this->swooleDir ?>:<?=$this->workDir?>/ext/swoole phpswoole/swoole_cli_os:<?= $this->dockerVersion ?> /bin/bash
    exit 0
elif [ "$1" = "all-library" ] ;then
    make_all_library
<?php foreach ($this->libraryList as $item) : ?>
elif [ "$1" = "<?=$item->name?>" ] ;then
    make_<?=$item->name?> && echo "[SUCCESS] make <?=$item->name?>"
elif [ "$1" = "clean-<?=$item->name?>" ] ;then
    clean_<?=$item->name?> && echo "[SUCCESS] make clean <?=$item->name?>"
<?php endforeach; ?>
elif [ "$1" = "config" ] ;then
    config_php
elif [ "$1" = "build" ] ;then
    make_php
elif [ "$1" = "archive" ] ;then
    cd bin
    SWOOLE_VERSION=$(./swoole-cli -r "echo SWOOLE_VERSION;")
    SWOOLE_CLI_FILE=swoole-cli-v${SWOOLE_VERSION}-linux-x64.tar.xz
    strip swoole-cli
    tar -cJvf ${SWOOLE_CLI_FILE} swoole-cli LICENSE
    mv ${SWOOLE_CLI_FILE} ../
    cd -
elif [ "$1" = "clean-all-library" ] ;then
<?php foreach ($this->libraryList as $item) : ?>
    clean_<?=$item->name?> && echo "[SUCCESS] make clean [<?=$item->name?>]"
<?php endforeach; ?>
elif [ "$1" = "diff-configure" ] ;then
  meld $SRC/configure.ac ./configure.ac
elif [ "$1" = "pkg-check" ] ;then
<?php foreach ($this->libraryList as $item) : ?>
    echo "[<?= $item->name ?>]"
    pkg-config --libs <?= ($item->pkgName ?: $item->name) . PHP_EOL ?>
    echo "==========================================================="
<?php endforeach; ?>
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
  cp -r $SRC/build ./
  cp -r ./TSRM/TSRM.h main/TSRM.h
  cp -r $SRC/configure.ac ./
  exit 0
else
    help
fi


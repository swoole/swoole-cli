#!/usr/bin/env bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../../
  pwd
)
cd ${__PROJECT__}


export PATH=/usr/bin/:$PATH

bash ./sapi/scripts/cygwin/cygwin-config-ext.sh --php-version 8.2.29
PHP_SRC_EXT_DIR=${__PROJECT__}/var/cygwin-build/php-src/ext

test -d /tmp/php-src-ext && rm -rf /tmp/php-src-ext
mv $PHP_SRC_EXT_DIR /tmp/php-src-ext
mkdir -p $PHP_SRC_EXT_DIR
cd /tmp/php-src-ext
test -d date && cp -rf date $PHP_SRC_EXT_DIR
test -d hash && cp -rf hash $PHP_SRC_EXT_DIR
test -d json && cp -rf json $PHP_SRC_EXT_DIR
test -d pcre && cp -rf pcre $PHP_SRC_EXT_DIR
test -d standard && cp -rf standard $PHP_SRC_EXT_DIR
test -d reflection && cp -rf reflection $PHP_SRC_EXT_DIR
test -d spl && cp -rf spl $PHP_SRC_EXT_DIR
test -d tokenizer && cp -rf tokenizer $PHP_SRC_EXT_DIR
test -d session && cp -rf session $PHP_SRC_EXT_DIR
test -d random && cp -rf random $PHP_SRC_EXT_DIR
test -d phar && cp -rf phar $PHP_SRC_EXT_DIR
test -d iconv && cp -rf iconv $PHP_SRC_EXT_DIR
cd ${__PROJECT__}

bash ./sapi/scripts/cygwin/cygwin-config.sh
bash ./sapi/scripts/cygwin/cygwin-build.sh

exit 0
cat ${__PROJECT__}/var/cygwin-build//php-src/config.log
cat var/cygwin-build//php-src/Makefile | grep 'link'
cat var/cygwin-build//php-src/Makefile | grep '\-\-mode=link'



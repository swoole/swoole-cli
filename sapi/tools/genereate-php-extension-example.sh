#!/usr/bin/env bash


:<<'COMMENT'

创建 PHP 扩展 辅助工具

COMMENT

__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
if [ -f ${__DIR__}/prepare.php ] ; then
  __PROJECT__=$(
    cd ${__DIR__}/
    pwd
  )
else
  __PROJECT__=$(
    cd ${__DIR__}/../../
    pwd
  )
fi

cd ${__DIR__}


export PATH="${__PROJECT__}/bin/runtime:$PATH"

alias php='php -c ${__PROJECT__}/bin/runtime/php.ini'
php ${__PROJECT__}/sapi/scripts/DownloadPHPSourceCode.php


php ${__PROJECT__}/var/php-8.2.13/ext/ext_skel.php --help

# 创建扩展
# 使用 `--dir ${__PROJECT__}/../ ` # 指定生成扩展的目录

php ${__PROJECT__}/var/php-8.2.13/ext/ext_skel.php  --ext example --author jingjingxyk --std


#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../
  pwd
)
cd ${__DIR__}
cd ${__PROJECT__}


export http_proxy=http://127.0.0.1:1087
export https_proxy=http://127.0.0.1:1087

pear config-set http_proxy $http_proxy
pecl config-show

# SKIP_LIBRARY_DOWNLOAD=1 php prepare.php +mongodb +inotify
# php prepare.php  +mongodb +inotify
php prepare.php   +inotify

pear config-set http_proxy ''



chmod a+x ./make.sh

chown -R 1000:1000 .

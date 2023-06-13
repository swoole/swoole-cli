#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../
  pwd
)
cd ${__PROJECT__}

GIT_BRANCH=$(git branch | grep '* ' | awk '{print $2}')
echo $GIT_BRANCH
ACTION=""
case $GIT_BRANCH in
'build_native_php')
  ACTION="delete"
  ;;
'build_php_8.2')
  ACTION="delete"
  ;;
'build_php_7.4')
  ACTION="delete"
  ;;
'build_php_7.3')
  ACTION="delete"
  ;;
'build_native_php_sfx_micro')
  ACTION="delete"
  ;;
*)
  echo 'no need delete ext '
  ;;

esac

if [ $ACTION = "delete" ]; then
  test -d ext/ && rm -rf ext

  test -d Zend/ && rm -rf Zend/
  test -d build && rm -rf build
  test -d TSRM && rm -rf TSRM
  test -d autom4te.cache && rm -rf autom4te.cache
  test -d main && rm -rf main
  test -d libs && rm -rf libs
  test -d include && rm -rf include
  test -d pear && rm -rf pear
  test -d modules && rm -rf modules
  test -f configure && rm -rf configure
  test -f configure.ac && rm -rf configure.ac
  test -f .gitmodules && rm -rf .gitmodules
  test -f buildconf && rm -rf buildconf

fi

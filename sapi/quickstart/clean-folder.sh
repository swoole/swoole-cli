#!/bin/bash

set -ex
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../
  pwd
)
cd ${__PROJECT__}

if [ ! "$BASH_VERSION" ] ; then
    echo "Please use bash to run this script (bash $0)" 1>&2
    exit 1
fi

if [[ -f /.dockerenv ]]; then
  git config --global --add safe.directory ${__PROJECT__}
fi

GIT_BRANCH=$(git branch | grep '* ' | awk '{print $2}')
echo $GIT_BRANCH
ACTION="none"
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
'build_php_7.2')
  ACTION="delete"
  ;;
'build_native_php_sfx_micro')
  ACTION="delete"
  ;;
*)
  echo 'no need delete ext '
  ;;

esac

if [[ $ACTION = "delete" ]]; then
  cd ${__PROJECT__}
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
  test -f cppflags.log && rm -rf cppflags.log
  test -f ldflags.log && rm -rf ldflags.log
  test -f Makefile && rm -rf Makefile
  test -f Makefile.fragments && rm -rf Makefile.fragments
  test -f Makefile.objects && rm -rf Makefile.objects
  test -f config.log && rm -rf config.log
  test -f config.nice && rm -rf config.nice
  test -f config.status && rm -rf config.status
  test -f libtool && rm -rf libtool
  test -f conftest && rm -rf conftest
  test -f conftest.c && rm -rf conftest.c
  test -d scripts && rm -rf scripts

fi

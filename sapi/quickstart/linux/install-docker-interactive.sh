#!/bin/bash

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


# install docker
number=$(which docker  | wc -l)
if test $number -eq 0 ;then
    while true
    do
      read -r -p "Install docker Are You Sure? [Y/n] " input
      case $input in
          [yY][eE][sS]|[yY])
          echo "Yes"
              if [ "$MIRROR" = 'china' ] ; then
                  sh sapi/quickstart/install-docker.sh --mirror china
              else
                  sh sapi/quickstart/install-docker.sh
              fi
              exit;
          ;;

          [nN][oO]|[nN])
          echo "No"
          exit 0
          ;;

          *)
          echo "Invalid input..."
          ;;
      esac
    done
fi

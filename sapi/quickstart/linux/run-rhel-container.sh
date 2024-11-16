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
cd ${__DIR__}

{
  docker stop swoole-cli-rhel-dev
  sleep 5
} || {
  echo $?
}
cd ${__DIR__}

# IMAGE=oraclelinux:9

IMAGE=almalinux:9
IMAGE=rockylinux:9

OS="rocky"
MIRROR=''
while [ $# -gt 0 ]; do
  case "$1" in
  --os)
    OS="$2"
      ;;
  --mirror)
    MIRROR="$2"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done



case "$OS" in
    rocky)
      IMAGE=rockylinux:9
      case "$MIRROR" in
            china | openatom)
              IMAGE="hub.atomgit.com/library/rockylinux:9"
              ;;
      esac
      ;;
    alma)
      IMAGE=almalinux:9
      case "$MIRROR" in
            china | openatom)
              IMAGE="hub.atomgit.com/library/almalinux:9"
              ;;
      esac
      ;;
esac


cd ${__DIR__}
docker run --rm --name swoole-cli-rhel-dev -d -v ${__PROJECT__}:/work -w /work --init $IMAGE tail -f /dev/null

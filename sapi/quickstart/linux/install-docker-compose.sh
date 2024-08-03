#!/usr/bin/env bash

set -ex
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../../
  pwd
)
cd ${__PROJECT__}

if ! docker info >/dev/null 2>&1; then
  echo "This script uses docker, and it isn't running - please start docker and try again!"
  echo "Docker does not seem to be running, run it first and retry"
  exit 1
fi

docker info
ls -lh /usr/libexec/docker/cli-plugins/

if [ ! -f /usr/libexec/docker/cli-plugins/docker-compose ]; then

  # show more version info
  # https://github.com/docker/compose/releases
  VERSION="v2.29.1"

  curl -fsSL "https://github.com/docker/compose/releases/download/${VERSION}/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose

  chmod +x /usr/local/bin/docker-compose

fi

docker-compose --version

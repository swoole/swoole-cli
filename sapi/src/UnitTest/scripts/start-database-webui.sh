#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)

cd ${__DIR__}

docker-compose -f docker-compose-database-webui.yaml  up -d

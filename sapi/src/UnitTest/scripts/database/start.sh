#!/usr/bin/env bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)

cd ${__DIR__}

docker-compose -f docker-compose.yaml up -d
docker-compose -f docker-compose.yaml ps

docker container ls -a
docker inspect --format='{{json .State.Status}}' "postgresql"

while ! nc -z 127.0.0.1 5432; do sleep 1; done
echo "Can you connect to a PostgreSQL database."

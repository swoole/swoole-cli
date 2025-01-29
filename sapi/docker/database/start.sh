#!/usr/bin/env bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)

cd ${__DIR__}

docker compose -f docker-compose.yaml up -d
docker compose -f docker-compose.yaml ps

docker container ls -a

# wait postgresql ready

docker inspect --format='{{json .State.Status}}' "postgresql"

# docker exec -i postgresql cat /usr/local/bin/docker-entrypoint.sh

docker inspect --format="{{json .State.Health }}" "postgresql"

PG_HEALTH_STATUS=$(docker inspect --format='{{json .State.Health.Status }}' 'postgresql' | tr -d "\"")

until [ "${PG_HEALTH_STATUS}" = "healthy" ]; do
  sleep 3
  PG_HEALTH_STATUS=$(docker inspect --format='{{json .State.Health.Status }}' 'postgresql' | tr -d "\"")
done

while ! nc -z 127.0.0.1 5432; do sleep 1; done
echo "Can you connect to a PostgreSQL database."

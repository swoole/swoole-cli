#!/usr/bin/env bash

__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}
set -uex

RESULT_CODE=0
while [ $RESULT_CODE -eq 0 ]; do
  curl -Lo /dev/null http://10.1.20.4/all-deps.zip
  RESULT_CODE=$?
  sleep 1  # 每隔1秒检查一次
done




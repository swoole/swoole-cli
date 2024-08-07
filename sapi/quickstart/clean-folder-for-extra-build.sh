#!/usr/bin/env bash


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
echo "current  git branch : "$GIT_BRANCH

if [ $GIT_BRANCH = 'new_dev' ] ;then
  echo ' Deleting  folder is not allow in this branch : ' $GIT_BRANCH ;
  exit 0
fi

echo '正在执行删除无关的文件或者文件夹'

cd ${__DIR__}/linux/

test -d SDS && rm -rf SDS
test -d kubernetes && rm -rf kubernetes
test -d qemu && rm -rf qemu
test -d SDN && rm -rf SDN

cd ${__PROJECT__}/sapi/

test -d tools && rm -rf tools

cd ${__PROJECT__}/sapi/src/builder/

test -d library_shared && rm -rf library_shared

cd ${__PROJECT__}/sapi/src/

test -d library_builder && rm -rf library_builder

cd ${__PROJECT__}/sapi/docker/

test -d database && rm -rf database

test -d database-ui && rm -rf database-ui

test -d elasticsearch && rm -rf elasticsearch

test -d grafana && rm -rf grafana

test -d minio && rm -rf minio

test -d mysql && rm -rf mysql

test -d neo4j && rm -rf neo4j

test -d nginx && rm -rf nginx

test -d postgis && rm -rf postgis

test -d rabbitmq && rm -rf rabbitmq

test -d redis && rm -rf redis


cd ${__PROJECT__}/.github/workflows
test -f ceph.yml  && rm -rf ceph.yml
test -f kubernetes.yml  && rm -rf kubernetes.yml
test -f ovn.yml  && rm -rf ovn.yml
test -f ovn.yml  && rm -rf ovn.yml

cd ${__PROJECT__}/sapi/quickstart
test -d swoole-install && rm -rf swoole-install

cd ${__PROJECT__}
test -f setup-aria2-runtime.sh    && rm -rf setup-aria2-runtime.sh
test -f setup-coturn-runtime.sh   && rm -rf setup-coturn-runtime.sh
test -f setup-ffmpeg-runtime.sh   && rm -rf setup-ffmpeg-runtime.sh
test -f setup-go-runtime.sh && rm -rf setup-go-runtime.sh
test -f setup-nginx-runtime.sh    && rm -rf setup-nginx-runtime.sh
test -f setup-nodejs-runtime.sh    && rm -rf setup-nodejs-runtime.sh
test -f setup-php-cli-runtime.sh  && rm -rf setup-php-cli-runtime.sh
test -f setup-php-fpm-runtime.sh  && rm -rf setup-php-fpm-runtime.sh
test -f setup-privoxy-runtime.sh  && rm -rf setup-privoxy-runtime.sh
test -f setup-socat-runtime.sh    && rm -rf setup-socat-runtime.sh
test -f setup-supervisord.sh      && rm -rf setup-supervisord.sh
test -f setup-swoole-cli-pre-runtime.sh    && rm -rf setup-swoole-cli-pre-runtime.sh
test -f setup-webBenchmark-runtime.sh    && rm -rf setup-webBenchmark-runtime.sh
test -f setup-swow-cli-runtime.sh    && rm -rf setup-swow-cli-runtime.sh
test -f setup-php-fpm-7.4-runtime.sh    && rm -rf setup-php-fpm-7.4-runtime.sh
test -f setup-swoole-cli-runtime.sh    && rm -rf setup-swoole-cli-runtime.sh
test -f setup-php-cli-7.4-runtime.sh   && rm -rf setup-php-cli-7.4-runtime.sh
test -f setup-php-cli-7.3-runtime.sh   && rm -rf setup-php-cli-7.3-runtime.sh


cd ${__PROJECT__}

echo '删除完毕'
echo ''



#!/usr/bin/env bash

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

cd ${__PROJECT__}/

ldd ${__PROJECT__}/bin/swoole-cli.exe

cd ${__PROJECT__}
APP_VERSION=$(${__PROJECT__}/bin/swoole-cli.exe -v | head -n 1 | awk '{ print $2 }')
NAME="swoole-cli-v${APP_VERSION}-msys2-x64"

test -d /tmp/${NAME} && rm -rf /tmp/${NAME}
mkdir -p /tmp/${NAME}/
mkdir -p /tmp/${NAME}/etc/
mkdir -p /tmp/${NAME}/bin/

cd ${__PROJECT__}/
ldd ${__PROJECT__}/bin/swoole-cli.exe | grep -v '/c/Windows/' | awk '{print $3}'
ldd ${__PROJECT__}/bin/swoole-cli.exe | grep -v '/c/Windows/' | awk '{print $3}' | xargs -I {} cp {} /tmp/${NAME}/bin/

ls -lh /tmp/${NAME}/

cp -f ${__PROJECT__}/bin/swoole-cli.exe /tmp/${NAME}/bin/
# cp -f ${__PROJECT__}/bin/LICENSE /tmp/${NAME}/
# cp -f ${__PROJECT__}/bin/credits.html /tmp/${NAME}/

cp -rL /etc/pki/ /tmp/${NAME}/etc/

cd /tmp/${NAME}/etc/
test -f cacert.pem || curl -LSo cacert.pem https://curl.se/ca/cacert.pem

cd /tmp/${NAME}/

test -f ${__PROJECT__}/${NAME}.zip && rm -f ${__PROJECT__}/${NAME}.zip
zip -r ${__PROJECT__}/${NAME}.zip .

ls -lha ${__PROJECT__}/${NAME}.zip

cd ${__PROJECT__}

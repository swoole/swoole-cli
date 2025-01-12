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
NAME="swoole-cli-v${APP_VERSION}-cygwin-x64"

test -d /tmp/${NAME} && rm -rf /tmp/${NAME}
mkdir -p /tmp/${NAME}
mkdir -p /tmp/${NAME}/etc/

cd ${__PROJECT__}/
ldd ${__PROJECT__}/bin/swoole-cli.exe | grep -v '/cygdrive/' | awk '{print $3}'
ldd ${__PROJECT__}/bin/swoole-cli.exe | grep -v '/cygdrive/' | awk '{print $3}' | xargs -I {} cp {} /tmp/${NAME}/

ls -lh  /tmp/${NAME}/

cp -f ${__PROJECT__}/bin/swoole-cli.exe /tmp/${NAME}/
# cp -f ${__PROJECT__}/bin/LICENSE /tmp/${NAME}/
# cp -f ${__PROJECT__}/bin/credits.html /tmp/${NAME}/

cp -rL /etc/pki/ /tmp/${NAME}/etc/

cd /tmp/${NAME}/

test -f ${__PROJECT__}/${NAME}.zip && rm -f ${__PROJECT__}/${NAME}.zip
zip -r ${__PROJECT__}/${NAME}.zip .

cd ${__PROJECT__}

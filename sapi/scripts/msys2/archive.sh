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
ldd ${__PROJECT__}/bin/php.exe

cd ${__PROJECT__}
APP_VERSION=$(${__PROJECT__}/bin/php.exe -v | head -n 1 | awk '{ print $2 }')
NAME="php-cli-v${APP_VERSION}-msys2-x64"

test -d /tmp/${NAME} && rm -rf /tmp/${NAME}
mkdir -p /tmp/${NAME}/
mkdir -p /tmp/${NAME}/etc/
mkdir -p /tmp/${NAME}/bin/

cd ${__PROJECT__}/
ldd ${__PROJECT__}/bin/php.exe | grep -v '/c/Windows/' | awk '{print $3}'
ldd ${__PROJECT__}/bin/php.exe | grep -v '/c/Windows/' | awk '{print $3}' | xargs -I {} cp {} /tmp/${NAME}/

ls -lh /tmp/${NAME}/

cp -f ${__PROJECT__}/bin/php.exe /tmp/${NAME}/bin/
# cp -f ${__PROJECT__}/bin/LICENSE /tmp/${NAME}/
# cp -f ${__PROJECT__}/bin/credits.html /tmp/${NAME}/

cp -rL /etc/pki/ /tmp/${NAME}/etc/

cd /tmp/${NAME}/

test -f ${__PROJECT__}/${NAME}.zip && rm -f ${__PROJECT__}/${NAME}.zip
zip -r ${__PROJECT__}/${NAME}.zip .

cd ${__PROJECT__}


#!/usr/bin/env bash

set -eux
__CURRENT__=`pwd`
__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}
mkdir -p ssl

openssl req -nodes -new -x509 -subj "/CN=localhost" -keyout ./ssl/private/website.key -out ./ssl/certs/website.crt


# 参考 https://gist.github.com/soarez/9688998
# 参考 https://github.com/jingjingxyk/swoole-cli/issues/149
# 参考 https://book.hacktricks.xyz/generic-methodologies-and-resources/tunneling-and-port-forwarding#ssl-socat-tunnel

#!/usr/bin/env sh

if [ -z "$1" ] || [ -z "$2" ] || [ -n "$3" ]; then
  echo 'no found parameter $1 $2 '
  exit 1
fi

# environment deps

# apk add netcat-openbsd
# apk add socat



# HTTP PROXY

# shellcheck disable=SC2034
HTTP_PROXY_HOST=192.168.3.26
# shellcheck disable=SC2034
HTTP_PROXY_PORT=8015


# SOCKS PROXY
# shellcheck disable=SC2034
SOCKS_PROXY_HOST=192.168.3.26
# shellcheck disable=SC2034
SOCKS_PROXY_PORT=2000



# nc -h


nc -X connect  -x $HTTP_PROXY_HOST:$HTTP_PROXY_PORT "$1" "$2"

# nc -X 5  -x $SOCKS_PROXY_HOST:$SOCKS_PROXY_PORT "$1" "$2"


# socat -h
# exec socat stdio  PROXY:$HTTP_PROXY_HOST:$1:$2,proxyport=$HTTP_PROXY_PORT
# exec socat STDIO socks4a:$SOCKS_PROXY_HOST:$1:$2,socksport=$SOCKS_PROXY_PORT

# socat - PROXY:$HTTP_PROXY_HOST:$1:$2,proxyport=$HTTP_PROXY_PORT

# socat - socks4a:$SOCKS_PROXY_HOST:$1:$2,socksport=$SOCKS_PROXY_PORT










# exec /usr/bin/nc -X 5 -x <socks_host>:<socks_port> $1 $2

# exec nc -5 -S <socks_proxy>:<port> $*
# shellcheck disable=SC2093
# exec nc -5 -S $PROXY_HOST:$PROXY_PORT $*

:<<'EOF'
if [ -z "$SOCAT" ]; then
	SOCAT=$(which socat 2>/dev/null)
	if [ $? -ne 0 ]; then
		echo "ERROR: socat binary not in PATH" 1>&2
		exit 1
	fi
fi


socat - PROXY:your.proxy.ip:%h:%p,proxyport=8015,proxyauth=user:pwd


EOF


#!/usr/bin/env sh

if [ -z "$1" ] || [ -z "$2" ] || [ -n "$3" ]; then
  echo 'no found parameter $1 $2 '
  exit 1
fi

# connect through the specifid proxy
# nc -X connect  -x 192.168.3.26:8015 "$1" "$2"

# exec /usr/bin/nc -X 5 -x <socks_host>:<socks_port> $1 $2




if [ -z "$SOCAT" ]; then
	SOCAT=$(which socat 2>/dev/null)
	if [ $? -ne 0 ]; then
		echo "ERROR: socat binary not in PATH" 1>&2
		exit 1
	fi
fi


:<<'EOF'
# http proxy
PROXY_HOST=192.168.3.26
PROXY_PORT=8015
# shellcheck disable=SC2093
exec socat STDIO  PROXY:${PROXY_HOST}:$1:$2,proxyport=${PROXY_PORT}
EOF

# socks5
PROXY_HOST=192.168.3.26
PROXY_PORT=2000
exec socat STDIO socks4a:${PROXY_HOST}:$1:$2,socksport=${PROXY_PORT}




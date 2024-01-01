ip='sdn.jingjingxyk.com'
keyfile=/home/jingingxyk/private-key.pem

{
  ssh -o StrictHostKeyChecking=no \
    -o ExitOnForwardFailure=yes \
    -o TCPKeepAlive=yes \
    -o ServerAliveInterval=15 \
    -o ServerAliveCountMax=3 \
    -i $keyfile \
    -v -CTgN \
    -R 172.23.149.62:2000:127.0.0.1:9501 \
    root@$ip
} || {
  echo $?

}


# curl -x socks5h://127.0.0.1:2000 -v https://www.google.com

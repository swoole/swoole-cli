:<<EOF
socks4://	SOCKS4 proxy, DNS resolution via client
socks4h://	SOCKS4 proxy, DNS resolution via remote system
socks5://	SOCKS5 proxy, DNS resolution via client
socks5h://	SOCKS5 proxy, DNS resolution via remote system

EOF


ssh -A -D 2000 USER@HOST_IP -i private.key


git -c http.proxy=socks5h://localhost:2000 clone -b new_dev https://github.com/jingjingxyk/swoole-cli.git



cat > ~/.gitconfig <<==EOF
[https]
proxy = 'socks5://127.0.0.1:8015'
[http]
proxy = 'socks5://127.0.0.1:8015'
==EOF




cat > ~/.ssh/config <<==EOF
Host github.com
Hostname github.com
ServerAliveInterval 55
ForwardAgent yes
ProxyCommand nc -X 5 -x 127.0.0.1:1080 %h %p
ProxyCommand nc -X connect -x localhost:8015 %h %p
proxycommand socat - PROXY:your.proxy.ip:%h:%p,proxyport=8015,proxyauth=user:pwd
==EOF



git config --global -l
git config --global --list

git config --global core.gitproxy "git-proxy"
git config --global socks.proxy "localhost:1080"


git config --global --unset http.proxy
git config --global --unset https.proxy
git config --global --unset core.gitproxy
git config --global --unset safe.directory /work

nc -X 5 -x 127.0.0.1:1080 "$@"




export GIT_SSH_COMMAND='ssh -o ProxyCommand="nc -X 5 -x 127.0.0.1:1080 %h %p"'


git config --global core.gitproxy ""

git config --global core.gitproxy "git-proxy"
# git config --global core.gitproxy "/usr/bin/git-proxy"

git config --global core.gitproxy "/work/bin/runtime/git-proxy"

sed 's@directory = /work@@g' ~/.gitconfig


sh sapi/quickstart/deploy-git-proxy.sh

# 参考
# https://bryanbrattlof.com/how-to-proxy-git-connections/

# https://elinux.org/Using_git_with_a_proxy

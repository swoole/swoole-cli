set -x

mkdir -p /etc/apt/apt.conf.d/

test -f /etc/apt/apt.conf.d/proxy.conf && rm -rf /etc/apt/apt.conf.d/proxy.conf

cat >/etc/apt/apt.conf.d/proxy.conf <<EOF
Acquire::http::Proxy  "http://192.168.3.26:8015";
Acquire::https::Proxy "http://192.168.3.26:8015";

EOF

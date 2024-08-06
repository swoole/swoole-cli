# 换源帮助
# https://mirrors.ustc.edu.cn/help/freebsd-pkg.html

cat /etc/pkg/FreeBSD.conf

# cp -f /etc/pkg/FreeBSD.conf /etc/pkg/FreeBSD.conf.bak

# 13 以前的版本，需要这样设置，
# sed  -i.t "s@pkg+http://pkg.FreeBSD.org/@https://mirrors.ustc.edu.cn/freebsd-pkg/@"  /etc/pkg/FreeBSD.conf
# sed  -i.t 's@"srv"@"none"@'  /etc/pkg/FreeBSD.conf

mkdir -p /usr/local/etc/pkg/repos/



cat > /usr/local/etc/pkg/repos/FreeBSD.conf <<"EOF-CONFIG"
FreeBSD: {
  url: "http://mirrors.ustc.edu.cn/freebsd-pkg/${ABI}/quarterly",
}

"EOF-CONFIG"

cat  /usr/local/etc/pkg/repos/FreeBSD.conf

# pkg update -f 更新索引
env ASSUME_ALWAYS_YES=YES pkg update -f
env ASSUME_ALWAYS_YES=YES pkg update
env ASSUME_ALWAYS_YES=YES pkg upgrade
env ASSUME_ALWAYS_YES=YES pkg bootstrap -f
env ASSUME_ALWAYS_YES=YES pkg install git curl cmake wget openssl socat
env ASSUME_ALWAYS_YES=YES pkg install gcc
env ASSUME_ALWAYS_YES=YES pkg install llvm


# 版本需要大于等于 13
# https://mirrors.ustc.edu.cn/freebsd-pkg/
# https://pkg.freebsd.org/

# freebsd-update fetch
# freebsd-update install

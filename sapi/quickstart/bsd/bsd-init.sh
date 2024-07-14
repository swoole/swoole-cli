# 换源帮助
# https://mirrors.ustc.edu.cn/help/freebsd-pkg.html

cat /etc/pkg/FreeBSD.conf

# cp -f /etc/pkg/FreeBSD.conf /etc/pkg/FreeBSD.conf.bak

# sed  -i.t "s@pkg+http://pkg.FreeBSD.org/@https://mirrors.ustc.edu.cn/freebsd-pkg/@"  /etc/pkg/FreeBSD.conf
# sed  -i.t 's@"srv"@"none"@'  /etc/pkg/FreeBSD.conf

mkdir -p /usr/local/etc/pkg/repos/



cat > /usr/local/etc/pkg/repos/FreeBSD.conf <<"EOF-CONFIG"
FreeBSD: {
  url: "http://mirrors.ustc.edu.cn/freebsd-pkg/${ABI}/quarterly",
}

"EOF-CONFIG"

cat  /usr/local/etc/pkg/repos/FreeBSD.conf


env ASSUME_ALWAYS_YES=YES pkg update -f
env ASSUME_ALWAYS_YES=YES pkg update
env ASSUME_ALWAYS_YES=YES pkg upgrade
env ASSUME_ALWAYS_YES=YES pkg bootstrap -f
env ASSUME_ALWAYS_YES=YES pkg install git curl cmake llvm wget openssl socat

## msys2 安装

# 打开站点，下载 msys2 安装包：
#
#  https://mirrors.tuna.tsinghua.edu.cn/msys2/distrib/x86_64/

wget   https://mirrors.tuna.tsinghua.edu.cn/msys2/distrib/x86_64/msys2-x86_64-20230526.exe

# msys2 help
# https://mirror.tuna.tsinghua.edu.cn/help/msys2/

# 换源
sed -i "s#https\?://mirror.msys2.org/#https://mirrors.tuna.tsinghua.edu.cn/msys2/#g" /etc/pacman.d/mirrorlist*

# 更新源
pacman -Sy --noconfirm
# 无须确认安装包
pacman -Sy --noconfirm git curl wget openssl zip unzip xz



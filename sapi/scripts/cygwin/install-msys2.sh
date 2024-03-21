## msys2 安装

# 打开站点：
#
#  https://mirrors.tuna.tsinghua.edu.cn/msys2/distrib/x86_64/

wget   https://mirrors.tuna.tsinghua.edu.cn/msys2/distrib/x86_64/msys2-x86_64-20230318.exe

# msys2 help
# https://mirror.tuna.tsinghua.edu.cn/help/msys2/


# 搜索包
pacman -Ss curl
# 升级
pacman -Syu
# 安装包
pacman -Sy git curl wget
# 无须确认安装包
pacman -Sy --noconfirm git curl wget openssl

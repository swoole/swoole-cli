
```bash

LC_ALL=C lscpu | grep Virtualization

egrep -c '(vmx|svm)' /proc/cpuinfo

sudo apt install qemu qemu-kvm virt-manager bridge-utils

sudo useradd -g $USER libvirt
sudo useradd -g $USER libvirt-kvm

sudo systemctl enable libvirtd.service && sudo systemctl start libvirtd.service


# 挂载共享目录
sudo mount -t virtiofs sharename path/to/shared/directory

sharename           path/to/shared/directory    virtiofs    defaults        0       0

# 共享剪贴板

sudo apt install spice-vdagent



```


[virtualization-qemu](https://ubuntu.com/server/docs/virtualization-qemu)


Remmina 是一款功能强大的自由开源的远程桌面客户端，支持多种协议，包括 RDP、VNC、SPICE、X2GO、SSH 和 HTTP(S)。

远程连接协议VNC/SPICE/RDP

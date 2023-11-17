
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



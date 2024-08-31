## download openwrt x86

    https://downloads.openwrt.org/releases/

    https://downloads.openwrt.org/releases/23.05.4/targets/x86/64/

## OpenWrt on VirtualBox
    https://openwrt.org/docs/guide-user/virtualization/virtualbox-vm

## virtualbox img 文件转换为 vdi
```shell

# img 文件转换为 vdi

VBoxManage convertfromraw  --format VDI ~/Downloads/openwrt/openwrt-23.05.4-x86-64-generic-ext4-combined.img ~/Downloads/openwrt/openwrt-23.05.4-x86-64-generic-ext4-combined.vdi

# 修改磁盘大小
VBoxManage modifyhd --resize 8096 ~/Downloads/openwrt/openwrt-23.05.4-x86-64-generic-ext4-combined.vdi

```

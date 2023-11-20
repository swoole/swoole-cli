## squashNFS-Ganesha 导出示例

> hhttps:
> //github.com/nfs-ganesha/nfs-ganesha/blob/next/src/config_samples/export.txt

> https://cloud.tencent.com/developer/article/2061497

> no_root_squash all_squash

## [NFS 挂载参考](https://help.aliyun.com/zh/nas/user-guide/mount-an-nfs-file-system-on-a-linux-ecs-instance)

## dev container
```bash
# 检查NFSv4.1内核中是否启用了支持
cat /boot/config-`uname -r`| grep CONFIG_NFS_V4_1

# 检查NFSv4.2内核中是否启用了支持
cat /boot/config-`uname -r`| grep CONFIG_NFS_V4_2


apt-get install nfs-common

yum install nfs-utils
```
```bash

cat ceph/src/cephadm/box/box.py | grep 'quay'

```

## ceph-ci

```text

CEPH_IMAGE = 'quay.ceph.io/ceph-ci/ceph:main'
BOX_IMAGE = 'cephadm-box:latest'

```


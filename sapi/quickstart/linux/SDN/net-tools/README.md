## 测试 UDP 端口是否开放

```shell
apt install -y netcat

brew install netcat-bsd

```

```shell

nc -u 192.168.3.110 6081


```

## 列出系统的开机启动项

```shell

systemctl list-unit-files --type=service | awk '$2 == "enabled" {print $0}'

```

## 列出制定目录隐藏文件

```shell

TargetDirPath=/data/
find $TargetDirPath -name ".*" -ls

```

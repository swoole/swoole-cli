参考 powershell 下载文件

```bash

Invoke-WebRequest -Uri "https://github.com/wixtoolset/wix3/releases/download/wix3141rtm/wix314-binaries.zip" -OutFile wix.zip
Expand-Archive -Path .\wix.zip -DestinationPath wix\bin

```

参考 github action windows 构建

https://github.com/OpenVPN/openvpn-build/blob/master/.github/workflows/build.yaml

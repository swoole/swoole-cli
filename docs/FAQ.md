## download  swoole-cli

```shell

curl -fSL https://github.com/swoole/swoole-cli/blob/main/setup-swoole-cli-runtime.sh?raw=true | bash -s -- --version  v6.2.0.0

# from https://www.swoole.com/download
curl -fSL https://github.com/swoole/swoole-cli/blob/main/setup-swoole-cli-runtime.sh?raw=true | bash -s -- --version  v6.2.0.0 --mirror china

```

## 备注： macos环境下 首次运行提示无权限 ，解决方法

note : macos clearing the com.apple.quarantine extended attribute

```
xattr ./runtime/swoole-cli/swoole-cli

sudo xattr -rd com.apple.quarantine  ./runtime/swoole-cli/swoole-cli

```


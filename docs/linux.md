构建镜像
====
`Linux` 下需要在容器中构建，因此需要先构建 `swoole-cli-builder:base` 基础镜像。
基础镜像 `Dockerfile` 参考 [sapi/Dockerfile](sapi/Dockerfile)

1. 构建完成之后，使用 `./make.sh docker-bash-init` 进入容器
2. 然后使用 `./make.sh all-library` 构建所有 `C/C++`库
3. 最后使用 `./make.sh docker-commit` 提交 `swoole-cli-builder` 镜像

> 当 `C库` 变更时，应该修改 `swoole-cli-builder` 镜像的版本

常见错误
=====

make: ext/opcache/minilua: No such file or directory
-----

解决办法：删除此文件，然后重新启动构建

```bash
rm ext/opcache/minilua
./make.sh build
```

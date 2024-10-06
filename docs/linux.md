# linux 环境下构建  swoole-cli

## 运行环境要求

1. 容器 docker 运行环境
2. debian 系列 要求linux 内核大于 5.0

## linux 环境下构建 完整步骤

```shell

git clone -b main https://github.com/swoole/swoole-cli.git
cd swoole-cli
git submodule update --init -f

bash sapi/quickstart/linux/install-docker.sh
bash sapi/quickstart/linux/run-alpine-container.sh
bash sapi/quickstart/linux/connection-swoole-cli-alpine.sh

sh sapi/quickstart/linux/alpine-init-minimal.sh
bash sapi/quickstart/linux/alpine-init.sh

bash setup-php-runtime.sh

__DIR__=$(pwd);
export PATH=${__DIR__}/bin/runtime/:$PATH
alias php="'${__DIR__}/bin/runtime/php -c ${__DIR__}/bin/runtime/php.ini'"

composer install  --no-interaction --no-autoloader --no-scripts --profile --no-dev
composer dump-autoload --optimize --profile --no-dev

php prepare.php  +apcu +ds +xlswriter +ssh2 +uuid

bash make-install-deps.sh

# 静态编译依赖库
bash make.sh  all-library

# 静态编译 PHP 预处理
bash make.sh config

# 静态编译PHP （编译、汇编、链接）
bash make.sh build

# 静态编译PHP （打包）
bash make.sh archive

./bin/swoole-cli -m
./bin/swoole-cli --ri swoole
file ./bin/swoole-cli

```

## 可使中国大陆软件镜像源命令脚本

```shell

# 准备 docker 运行时 使用镜像 （镜像源 mirrors.tuna.tsinghua.edu.cn）
bash sapi/quickstart/linux/install-docker.sh --mirror china

sh sapi/quickstart/linux/alpine-init-minimal.sh  --mirror china

bash sapi/quickstart/linux/alpine-init.sh --mirror china

# 准备PHP 运行时 使用镜像 （镜像源 https://www.swoole.com/download）
bash setup-php-runtime.sh --mirror china



```

## 可使用代理的命令脚本

```bash

# 准备PHP 运行时 使用代理
bash setup-php-runtime.sh --proxy http://192.168.3.26:8015

php prepare.php  +apcu +ds +xlswriter +ssh2 +uuid --with-http-proxy=socks5h://127.0.0.1:2000

```



## 快速生成 构建脚本 make.sh (跳过下载依赖库源码)

```shell

# 生成构建脚本 make.sh
php prepare.php  --without-docker --skip-download=1
bash ./make.sh docker-build
bash ./make.sh docker-bash

# 进入容器后需要再一次执行此命令
php prepare.php  +inotify +apcu +ds +xlswriter +ssh2 +uuid

```

构建镜像
====
`Linux` 下需要在容器中构建，因此需要先构建 `swoole-cli-builder:base` 基础镜像。
基础镜像 `Dockerfile` 参考 [sapi/Dockerfile](/sapi/docker/Dockerfile)

1. 构建基础镜像：`./make.sh docker-build [china|ustc|tuna] `
   ，也可以直接使用官方构建好的镜像 `docker pull phpswoole/swoole-cli-builder:base`
1. 构建完成之后，使用 `./make.sh docker-bash` 进入容器
2. 构建所有 `C/C++`库： `./make.sh all-library`
3. 提交镜像：`./make.sh docker-commit` 提交 `swoole-cli-builder` 镜像
4. 推送镜像：`./make.sh docker-push`

> 当 `C库` 变更时，应该修改 `swoole-cli-builder` 镜像的版本
> `make.sh all-library` 是可重入的，它会自动跳过已构建成功的库



构建 swoole-cli
====
需要依赖 `phpswoole/swoole-cli-builder` 镜像，可按照第一步的提示构建镜像，也可以直接拉取官方构建好的镜像。

- `phpswoole/swoole-cli-builder:base`：不包含 `C/C++` 库的基础镜像，需要自行构建 `./make.sh all-library`
- `phpswoole/swoole-cli-builder:1.6`：包含 `C/C++` 库的现成镜像，可直接构建 `swoole-cli`

1. 配置：`./make.sh config`，可能会出现缺失 `C/C++` 库，请检查相关的库是否正确编译安装
1. 编译：`./make.sh build`
2. 测试：`./make.sh test`，请注意此程序并没有运行 `php-src` 和 扩展的 `phpt` 测试文件，仅验证二进制文件的特性完整性
3. 打包：`./make.sh archive`

其他指令
====

* `./make.sh list-library`：列出所有 `C/C++` 库
* `./make.sh list-extension`：列出所有扩展
* `./make.sh clean-all-library`：清理所有 `C/C++` 库
* `./make.sh clean-all-library-cached`：清理所有 `C/C++` 库，保留缓存文件
* `./make.sh sync`：同步 `php-src` 版本
* `./make.sh pkg-check`：检查所有 `C/C++` 库
* `./make.sh list-swoole-branch`：列出 `swoole` 分支
* `./make.sh switch-swoole-branch`：切换 `swoole` 分支
* `./make.sh [library-name]`：单独编译某个 `C/C++` 库
* `./make.sh clean-[library-name]`：单独清理某个 `C/C++` 库
* `./make.sh clean-[library-name]-cached`：单独清理某个 `C/C++` 库，保留缓存文件

常见错误
=====

make: ext/opcache/minilua: No such file or directory
-----

解决办法：删除此文件，然后重新启动构建

```bash
rm ext/opcache/minilua
./make.sh build
```

docker no found
----
> 快速安装 docker 运行环境

```bash

bash sapi/quickstart/linux/install-docker.sh

# 使用中国镜像
bash sapi/quickstart/linux/install-docker.sh --mirror china


```

fix slow alpine apk installations
----

```bash

bash sapi/quickstart/linux/alpine-init.sh --mirror china

```

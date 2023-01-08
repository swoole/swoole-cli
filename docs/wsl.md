# 构建swoole-cli教程

## 简介

> Swoole-Cli 是一个 PHP 的二进制发行版，集成了 swoole、php 内核、php-cli、php-fpm以及多个常用扩展。Swoole-Cli 是全部静态编译打包的，不依赖任何操作系统的so动态链接库，具备非常好的移植性，可以在任意 Linux 系统之间复制，下载即可使用。

> 作为 PHP 开发者都应该知道 PHP 有两种运行模式：php-fpm和php-cli，那么在 Swoole 5.0中将迎来一种新的运行模式：swoole-cli。   
> Swoole 将像node.js这样作为独立程序提供给用户，而不是作为PHP的一个扩展。   
> 除此之外swoole-cli会尽可能地对php-src进行裁剪，移除一些不用的机制、模块、扩展、函数、类型、常量、代码，使得整个程序可以在几分钟之内编译完成。

以上两段引用自swoole作者韩天峰的介绍，总结来说就是swoole-cli将把PHP与各种扩展静态编译后打包，打包完成后swoole-cli可以作为一个程序分发到不同的系统中运行。

具体移除的内容与详细介绍可查看作者文章：[https://zhuanlan.zhihu.com/p/581695339](https://zhuanlan.zhihu.com/p/581695339)、[https://zhuanlan.zhihu.com/p/459983471](https://zhuanlan.zhihu.com/p/459983471)

目前swoole团队已开源构建工具：[https://github.com/swoole/swoole-cli](https://github.com/swoole/swoole-cli)

也可直接使用swoole团队编译好的版本：[https://www.swoole.com/download](https://www.swoole.com/download)

当前swoole-cli使用的php8.1，我们可以自行添加需要的扩展，或者修改指定的编译参数，自行构建自己的swoole-cli。

## 准备构建环境

Windows 环境推荐使用 WSL2。因为swoole-cli为了统一构建路径，使用了docker构建，WSL1下docker支持不完善。

构建环境需要使用php的pecl下载扩展文件和php-cli生成构建脚本。

此外因为需要频繁下载github文件，建议科学上网。

演示环境：

*   系统：WSL2-Ubuntu20.04

*   需要安装的包：php8.1 php8.1-dev php8.1-common php8.1-xml docker


分别执行下列命令检查环境是否正常

    php -v
    pecl version
    service docker status

## 构建步骤说明

在下面文档中，我们约定/swoole-cli目录即为宿主机构建工具所在目录，/work目录为容器内构建工具所在目录。

**1. git clone构建工具**

    git clone https://github.com/swoole/swoole-cli.git

可直接使用swoole团队的库，或者fork一个仓库。

**2. 进入swoole-cli目录，初始化git子模块**

    git submodule update --init --recursive

**3. 生成构建脚本**

    php prepare.php
    php prepare.php +mongodb
    php prepare.php +mongodb -bcmath

可使用\`+{ext}\`或者\`-{ext}\`增减扩展，如+mongodb代表需要mongodb扩展，-bcmath代表不使用bcmath扩展。

脚本会根据swoole-cli/conf.d目录中的配置自动下载相关的C/C++库以及PECL扩展，下载后的文件会放在swoole-cli/pool目录中。

**需要自定义扩展可以参考swoole-cli/conf.d/redis.php**

脚本执行完成后检查你需要的扩展是否已添加，**需要注意 Extension count 输出前面是否存在错误提示**。

执行成功会生成swoole-cli/make.sh，若maks.sh没有执行权限，执行一下命令增加执行权限：

    chmod +x make.sh

**常见错误：**

---

tar error：这是因为网络原因下载的包不完整导致的解压错误，下列解决方法参考处理：

*   直接删除swoole-cli/pool对应的包重新执行prepare脚本

*   按照swoole-cli/conf.d配置下载替换swoole-cli/pool对应的包，重新执行prepare脚本

*   按需自行构筑镜像服务器，替换对应库为镜像服务器下载地址，重新执行prepare脚本


---

**4. 进入 Docker Bash**

    ./make.sh docker-bash
    cd /work

:::
**注意在此步后的所有步骤均在Docker-Bash内执行，此步骤前的所有步骤均在宿主机上执行。**
:::

这一步会将 swoole-cli的目录映射到容器的 /work目录。

**5. 编译所有用到的库**

    ./make.sh all-library

所有编译后的库会放在/work/ext目录中。

**6. 编译配置**

    ./make.sh config

编译配置成功会输出：Thank you for using PHP

**常见错误：**

---

*   configure: error: Please reinstall the iconv library

*   configure:31121: error: Unable to locate gmp.h

*   fatal error: 'brotli/encode.h' file not found


这三类错误都是因为未编译或对应的库编译错误导致的，以Unable to locate gmp.h举例：

1.  ./make.sh clean\_gmp或./make.sh clean-all-library 清除已编译文件

2.  ./make.sh gmp 单独编译gmp库查看对应的编译错误提示解决对应编译错误


因为步骤3中wget各种库时，构建脚本没有作下载完整性检查，所以网络不顺畅的情况下下载的库可能不完整导致这一步发生各种编译错误，可以参考步骤3中tar error方法在宿主机中进行处理。

---

fatal error: 'ext/swoole/include/swoole version.h' file not found

未初始化子模块编译，参考[https://github.com/swoole/swoole-cli/issues/10](https://github.com/swoole/swoole-cli/issues/10)

---

configure: WARNING: unrecognized options: --enable-redis, --with-imagick, --enable-mongodb

这是因为php编译过程中有未识别到的编译参数，出现这些意味着扩展未成功编译到php中，需要检查两个方面：

1.  首先确认你的编译参数无误


> 是否用 --enable-extname 或 --with-extname 取决于扩展库

参考：[https://www.php.net/manual/zh/install.pecl.static.php](https://www.php.net/manual/zh/install.pecl.static.php)

2.  参考步骤3中tar error错误处理检查扩展包的完整性


---

**7. 构建**

    ./make.sh build

编译成功后会生成/work/bin/swoole-cli，执行下列命令检查是否编译正确

    ./bin/swoole-cli -m

**8. 打包**

    ./make.sh archive

打包成功后会生成/work/swoole-cli\*.tar.xz文件

## 使用说明

使用 ./swoole-cli -c 即可加载指定配置。

    ./swoole-cli -c /path/php.ini --ri swoole

把./swoole-cli -c 写入sh中，再给sh文件添加可执行权限，ln到/usr/bin中，即可每次执行命令时自动加载指定配置。
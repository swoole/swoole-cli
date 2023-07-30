

## 删除子模块
```bash
git submodule deinit ext/swoole

git submodule deinit -f ext/swoole

rm -rf .git/modules/ext/swoole/

git rm -rf ext/swoole/
```

## 创建空的新分支
```bash
git checkout  --orphan  static-php-cli-next

git rm -rf .

```
## 清理未跟踪的文件 谨慎使用
```bash
git clean -df
```

```bash
git clone --single-branch --depth=1 https://github.com/jingjingxyk/swoole-cli.git


git fsck --lost-found  # 查看记录
```

## 合并时不保留commit 信息
```bash
git merge --squash branch_name

```

## 当前分支 hash
```bash
git rev-parse HEAD

```

```bash

git config core.ignorecase false # 设置 Git 在 Windows 上也区分大小写

git reflog # 查看所有的提交历史
git reset --hard c761f5c # 回退到指定的版本

```


Linux man命令后数字含义  https://blog.csdn.net/u012424148/article/details/86227759

## 节省
```bash

git clone --recurse-submodules --single-branch -b main --progress --depth=1

```

```shell

pkg-config --libs libpq
pkg-config --cflags libpq
pkg-config libpq --libs-only-L
pkg-config --modversion libpq


pkg-config libpq --libs --cflags

```

--disable-new-dtags表示使用的是rpath，去掉后编译器默认使用runpath


使用 -Wl,–whole-archive -Wl,–start-group 和 -Wl,–end-group -Wl,-no-whole-archive

# 链接顺序问题
# 搜索 静态库的链接顺序
# 参考 https://bbs.huaweicloud.com/blogs/373470

https://eli.thegreenplace.net/2013/07/09/library-order-in-static-linking

https://bbs.huaweicloud.com/blogs/373470

https://ftp.gnu.org/old-gnu/Manuals/ld-2.9.1/html_node/ld_3.html

macos clang 不支持 -Wl,–whole-archive -Wl,–start-group 和 -Wl,–end-group -Wl,-no-whole-archive


gcc 提供了 -Wl,--as-needed 和 -Wl,--no-as-needed 两个选项，这两个选项一个是开启特性，一个是取消该特性。
-Wl,--as-needed 选项指示最终的可执行文件中只包含必要的链接库信息；
-Wl,--no-as-needed 选项指示在命令行中指定加载的所有库都记录到可执行文件头中，并最终由动态加载器去加载



#


Linux man命令后数字含义  https://blog.csdn.net/u012424148/article/details/86227759



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

re2c:
https://github.com/skvadrik/re2c/



https://github.com/multiarch/qemu-user-static
https://hub.docker.com/r/multiarch/qemu-user-static/tags?page=1&name=aarch64


ipv4 私有地址块
https://forum.huawei.com/enterprise/zh/thread/580934319407513600

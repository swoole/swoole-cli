常见错误
=====

make: ext/opcache/minilua: No such file or directory
-----

解决办法：删除此文件，然后重新启动构建

```bash
rm ext/opcache/minilua
./make.sh build
```

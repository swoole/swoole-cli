

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




## 删除子模块
```bash
git submodule deinit ext/swoole
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

#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../
  pwd
)
cd ${__PROJECT__}/bin/



# sudo apt install -y inotify-tools

# inotify 全平台的包 watchdog
# 参考文档 https://zhuanlan.zhihu.com/p/519945481
# 参考文档 https://www.cnblogs.com/wajika/p/6396748.html

# inotifywait -mrq -e create,modify,delete /home

if ! test -f ext-dependency-graph.graphviz.dot ;then
{
  echo "graphviz 模板文件 ext-dependency-graph.graphviz.dot 不存在"
  echo "请运行 php prepare.php --with-dependency-graph=1 命令生成模板文件"
  exit 3

}
fi


inotifywait --monitor --timefmt '%Y%m%dT%H%M%SZ' --format '%T%w%f' -e modify,create,attrib ${__PROJECT__}/bin/ext-dependency-graph.graphviz.dot \
    | while read file
    do
        echo "file changed, restart"
        echo $file
        {

          sh ${__DIR__}/generate-dependency-graph.sh
          echo "xdg-open ${__PROJECT__}/bin/ext-dependency-graph.svg"
          # chromium "${__PROJECT__}/bin/ext-dependency-graph.svg"
        } || {
          echo $?
        }

    done

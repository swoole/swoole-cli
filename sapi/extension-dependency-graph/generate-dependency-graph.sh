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
cd ${__DIR__}
cd ${__PROJECT__}/bin/

if test -f ext-dependency-graph.graphviz.dot; then
  {
    {
      dot -Tsvg ext-dependency-graph.graphviz.dot >${__PROJECT__}/bin/ext-dependency-graph.svg
      dot -Tpdf ext-dependency-graph.graphviz.dot >${__PROJECT__}/bin/ext-dependency-graph.pdf
      dot -Tjson ext-dependency-graph.graphviz.dot >${__PROJECT__}/bin/ext-dependency-graph.json
      dot -Tjpeg ext-dependency-graph.graphviz.dot >${__PROJECT__}/bin/ext-dependency-graph.jpeg
      dot -Twebp ext-dependency-graph.graphviz.dot >${__PROJECT__}/bin/ext-dependency-graph.webp
    } || {
      echo $?
    }

  }
else
  {
    echo "graphviz 模板文件  ext-dependency-graph.graphviz.dot 不存在，请生成模板文件"
    echo "graphviz 详细信息： https://www.graphviz.org/ "
  }
fi

## 生成更多格式的目标文件，比如webp  psd jpeg 格式等
## 详情： https://graphviz.org/docs/outputs/

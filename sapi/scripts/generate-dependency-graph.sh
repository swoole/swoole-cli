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

if test -f ext-dependency-graph.graphviz.dot ;then
{
   {
       dot -Tsvg   ext-dependency-graph.graphviz.dot > ${__PROJECT__}/bin/ext-dependency-graph.svg
       dot -Tpdf   ext-dependency-graph.graphviz.dot > ${__PROJECT__}/bin/ext-dependency-graph.pdf
   } || {
     echo $?
   }

}
else {
  echo "graphviz 模板文件  ext-dependency-graph.graphviz.dot 不存在，请生成模板文件"
  echo "graphviz 详细信息： https://www.graphviz.org/ "
}
fi


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


if test -f input.template.dot ;then
{
   {
       dot -Tsvg   input.template.dot > ${__PROJECT__}/bin/output.svg
       dot -Tpdf   input.template.dot > ${__PROJECT__}/bin/output.pdf
   } || {
     echo $?
   }

}
else {
  echo "graphviz 模板文件 input.template.dot 不存在，请生成模板文件"
  echo "graphviz 详细信息： https://www.graphviz.org/ "
}
fi


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




dot -Tsvg   input.template.dot > ${__PROJECT__}/bin/output.svg
dot -Tpdf   input.template.dot > ${__PROJECT__}/bin/output.pdf
#!/bin/env bash

set -exu

__PROJECT__=$(
cd "$(dirname "$0")"
pwd
)

cd ${__PROJECT__}

<?= $this->getProxyConfig() ?>

<?php if ($this->getInputOption('with-dependency-graph')) : ?>
# 生成扩展依赖图
sh sapi/extension-dependency-graph/generate-dependency-graph.sh
<?php endif; ?>

cd ${__PROJECT__}
bash sapi/download-box/download-dependencies-use-aria2.sh
cd ${__PROJECT__}
bash sapi/download-box/download-dependencies-use-git.sh

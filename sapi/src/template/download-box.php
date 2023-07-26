#!/bin/env bash

set -exu

__PROJECT__=$(
cd "$(dirname "$0")"
pwd
)

cd ${__PROJECT__}

<?= $this->getProxyConfig() ?>

bash sapi/scripts/download-dependencies-use-aria2.sh
bash sapi/scripts/download-dependencies-use-git.sh

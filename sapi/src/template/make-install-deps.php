#!/usr/bin/env bash
echo " install deps "

__CURRENT_DIR__=$(cd "$(dirname "$0")";pwd)
<?php if (in_array($this->buildType, ['dev','debug'])) : ?>
set -x
<?php  endif; ?>
<?= implode(PHP_EOL, $this->preInstallCommands) .PHP_EOL ?>

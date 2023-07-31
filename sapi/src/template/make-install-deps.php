#!/usr/bin/env bash
<?php if (in_array($this->buildType, ['dev','debug'])) : ?>
    set -x
<?php  endif; ?>


OS_RELEASE=$(awk -F= '/^ID=/{print $2}' /etc/os-release |tr -d '\n' | tr -d '\"')
__CURRENT_DIR__=$(cd "$(dirname "$0")";pwd)

echo " install deps "

function os_alpine_release() {
    echo "${OS_RELEASE}"
<?php foreach ($this->preInstallCommands['alpine'] as $item) :?>
    <?= implode(PHP_EOL, $item) . PHP_EOL ?>
<?php endforeach ;?>
    return 0
}

function os_debian_release() {
    echo "${OS_RELEASE}"
<?php foreach ($this->preInstallCommands['debian'] as $item) :?>
    <?= implode(PHP_EOL, $item) . PHP_EOL ?>
<?php endforeach ;?>
    return 0
}

function os_ubuntu_release() {
    echo "${OS_RELEASE}"
<?php foreach ($this->preInstallCommands['ubuntu'] as $item) :?>
    <?= implode(PHP_EOL, $item) . PHP_EOL ?>
<?php endforeach ;?>
    return 0
}

function os_macos_release() {
    echo "${OS_RELEASE}"
<?php foreach ($this->preInstallCommands['macos'] as $item) :?>
    <?= implode(PHP_EOL, $item) . PHP_EOL ?>
<?php endforeach ;?>
    return 0
}

if [ "$OS_RELEASE" = 'alpine' ]; then
    os_alpine_release
elif [ "$OS_RELEASE" = 'debian' ]; then
    os_debian_release
elif [ "$OS_RELEASE" = 'ubuntu' ]; then
    os_ubuntu_release
elif [ "$OS_RELEASE" = 'macos' ]; then
    os_macos_release
else
    echo 'no support OS'
fi

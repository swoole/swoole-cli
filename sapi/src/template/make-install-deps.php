#!/usr/bin/env bash
<?php if (in_array($this->buildType, ['dev','debug'])) : ?>
    set -x
<?php  endif; ?>

__CURRENT_DIR__=$(cd "$(dirname "$0")";pwd)

echo " install deps "

<?php if ($this->osType == 'macos') : ?>
    <?php foreach ($this->preInstallCommands['macos'] as $item) :?>
        <?= $item . PHP_EOL ?>
    <?php endforeach ;?>
<?php endif ;?>

<?php if ($this->osType == 'linux') : ?>
OS_RELEASE=$(awk -F= '/^ID=/{print $2}' /etc/os-release |tr -d '\n' | tr -d '\"')



function os_alpine_release() {
    echo "${OS_RELEASE}"
    <?php foreach ($this->preInstallCommands['alpine'] as $item) :?>
        <?= $item . PHP_EOL ?>
    <?php endforeach ;?>
    return 0
}

function os_debian_release() {
    echo "${OS_RELEASE}"
    <?php foreach ($this->preInstallCommands['debian'] as $item) :?>
        <?= $item . PHP_EOL ?>
    <?php endforeach ;?>
    return 0
}

function os_ubuntu_release() {
    echo "${OS_RELEASE}"
    <?php foreach ($this->preInstallCommands['ubuntu'] as $item) :?>
        <?= $item . PHP_EOL ?>
    <?php endforeach ;?>
    return 0
}


if [ "$OS_RELEASE" = 'alpine' ]; then
    os_alpine_release
elif [ "$OS_RELEASE" = 'debian' ]; then
    os_debian_release
elif [ "$OS_RELEASE" = 'ubuntu' ]; then
    os_ubuntu_release
else
    echo 'no support OS'
fi

<?php endif ;?>

#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';

use SwooleCli\Preprocessor;

$php_version_tag = trim(file_get_contents(__DIR__ . '/sapi/PHP-VERSION.conf'));
define('BUILD_PHP_VERSION', $php_version_tag);

$homeDir = getenv('HOME');
$p = Preprocessor::getInstance();
$p->parseArguments($argc, $argv);
$buildInContainer = $p->configureBuildEnvironment(__DIR__);
$buildType = $p->getBuildType();

# clean old make.sh
if (($buildType == 'dev') && file_exists(__DIR__ . '/make.sh')) {
    unlink(__DIR__ . '/make.sh');
}

// 在宿主机上直接创建 thirdparty 目录，并确保当前用户可写。
// 容器内构建始终是 root，可以往里写任何内容不受影响；依赖源码由宿主机下载，
// 若 thirdparty 由容器 root 创建（0755），宿主机普通用户将无法写入。
// 这里不假定执行 prepare.php 的具体用户，统一放宽为 0777 让所有用户可写。
$thirdpartyDir = __DIR__ . '/thirdparty';
if (!is_dir($thirdpartyDir)) {
    mkdir($thirdpartyDir, 0777, true);
    chmod($thirdpartyDir, 0777);
} elseif (!is_writable($thirdpartyDir)) {
    // thirdparty 已存在但当前用户不可写（例如之前由容器 root 创建）。
    // 普通用户无法修改 root 所有的目录，chmod 会失败，此时给出通用提示。
    @chmod($thirdpartyDir, 0777);
    if (!is_writable($thirdpartyDir)) {
        fwrite(STDERR, "thirdparty 目录不可写：{$thirdpartyDir}" . PHP_EOL
            . "请在构建容器内执行：chmod 777 /work/thirdparty" . PHP_EOL);
        exit(1);
    }
}

$varDir = __DIR__ . '/var';
if (!is_dir($varDir)) {
    mkdir($varDir, 0777, true);
    chmod($varDir, 0777);
} elseif (!is_writable($varDir)) {
    @chmod($varDir, 0777);
    if (!is_writable($varDir)) {
        fwrite(STDERR, "var 目录不可写：{$varDir}" . PHP_EOL
            . "请在构建容器内执行：chmod 777 /work/var" . PHP_EOL);
        exit(1);
    }
}

// 下载 php-src 源码（按 PHP-VERSION.conf 的版本，下载/解压到 var/php-<version>；
// 容器内通过挂载即 /work/var/php-<version>）
require __DIR__ . '/sapi/scripts/download-php-src-archive.php';

// Sync code from php-src
$p->setPhpSrcDir($p->getWorkDir() . '/var/php-' . BUILD_PHP_VERSION);

// 下载/更新 swoole-src（脚本内部按 SWOOLE-VERSION.conf 判断是否需要 checkout）
// 用 passthru 让脚本输出与退出码透传，下载失败（如网络超时）时能看到具体错误
if (!$p->isMobileTarget()) {
    $swoole_download_status = 0;
    passthru('bash ' . __DIR__ . '/sapi/scripts/download-swoole-src-archive.sh', $swoole_download_status);
    if ($swoole_download_status !== 0) {
        fwrite(STDERR, "download swoole-src failed with exit code: {$swoole_download_status}" . PHP_EOL);
        exit($swoole_download_status);
    }
}

// Generate make.sh
echo "build in container : " . ($buildInContainer ? 'yes' : 'no') . PHP_EOL;
echo "workDir   : " . $p->getWorkDir() . PHP_EOL;
echo "buildDir  : " . $p->getBuildDir() . PHP_EOL;
echo "phpSrcDir : " . $p->getPhpSrcDir() . PHP_EOL;
echo PHP_EOL;
echo "提示：Linux 下默认按容器内构建生成 make.sh（workDir=/work）；" . PHP_EOL;
echo "      若要在宿主机上直接编译，请显式指定 --without-docker" . PHP_EOL;
echo PHP_EOL;

$p->execute();

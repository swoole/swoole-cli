#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';

use SwooleCli\Preprocessor;

$php_version_tag = trim(file_get_contents(__DIR__ . '/sapi/PHP-VERSION.conf'));
define('BUILD_PHP_VERSION', $php_version_tag);

$homeDir = getenv('HOME');
$p = Preprocessor::getInstance();
$p->parseArguments($argc, $argv);

$buildType = $p->getBuildType();
if ($p->getInputOption('with-build-type')) {
    $buildType = $p->getInputOption('with-build-type');
    $p->setBuildType($buildType);
}

# clean old make.sh
if (($buildType == 'dev') && file_exists(__DIR__ . '/make.sh')) {
    unlink(__DIR__ . '/make.sh');
}

// Linux 下默认在构建容器中进行（workDir 为容器内的 /work）
// 只有显式指定 --without-docker 时才在宿主机上直接编译
$buildInContainer = !$p->getInputOption('without-docker');
if ($p->isMacos()) {
    // macOS 不使用构建容器，直接在宿主机编译
    $buildInContainer = false;
}

if (!$buildInContainer) {
    // Compile directly on the host machine, not in the docker container
    $p->setWorkDir(__DIR__);
    $p->setBuildDir(__DIR__ . '/thirdparty');
}

// --with-work-dir=DIR 优先级最高，用于显式覆盖工作目录
if ($p->getInputOption('with-work-dir')) {
    $workDir = rtrim($p->getInputOption('with-work-dir'), '/');
    $p->setWorkDir($workDir);
    $p->setBuildDir($workDir . '/thirdparty');
}

// 下载 php-src 源码（按 PHP-VERSION.conf 的版本，下载/解压到 var/php-<version>；
// 容器内通过挂载即 /work/var/php-<version>）
require __DIR__ . '/sapi/scripts/download-php-src-archive.php';

// Sync code from php-src
$p->setPhpSrcDir($p->getWorkDir() . '/var/php-' . BUILD_PHP_VERSION);

// 下载/更新 swoole-src（脚本内部按 SWOOLE-VERSION.conf 判断是否需要 checkout）
// 用 passthru 让脚本输出与退出码透传，下载失败（如网络超时）时能看到具体错误
$swoole_download_status = 0;
passthru('bash ' . __DIR__ . '/sapi/scripts/download-swoole-src-archive.sh', $swoole_download_status);
if ($swoole_download_status !== 0) {
    fwrite(STDERR, "download swoole-src failed with exit code: {$swoole_download_status}" . PHP_EOL);
    exit($swoole_download_status);
}

if ($p->getInputOption('with-global-prefix')) {
    $p->setGlobalPrefix($p->getInputOption('with-global-prefix'));
}

if ($p->getInputOption('with-parallel-jobs')) {
    $p->setMaxJob(intval($p->getInputOption('with-parallel-jobs')));
}

if ($p->isMacos()) {
    $p->setExtraLdflags('');
    exec("brew --prefix 2>&1", $output, $result_code);
    if ($result_code == 0) {
        $homebrew_prefix = trim(implode(' ', $output));
    } else {
        $homebrew_prefix = "";
    }
    $p->withBinPath($homebrew_prefix . '/opt/flex/bin')
        ->withBinPath($homebrew_prefix . '/opt/bison/bin')
        ->withBinPath($homebrew_prefix . '/opt/libtool/bin')
        ->withBinPath($homebrew_prefix . '/opt/m4/bin')
        ->withBinPath($homebrew_prefix . '/opt/automake/bin/')
        ->withBinPath($homebrew_prefix . '/opt/autoconf/bin/')
        ->withBinPath($homebrew_prefix . '/opt/gettext/bin')
        ->setLinker('ld');
    $p->setLogicalProcessors('$(sysctl -n hw.ncpu)');
} else {
    $p->setLinker('ld.lld');
    $p->setLogicalProcessors('$(nproc 2> /dev/null)');
}

$p->setExtraCflags(' -Os');

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


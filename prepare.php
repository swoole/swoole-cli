<?php

require __DIR__ . '/vendor/autoload.php';

use SwooleCli\Preprocessor;
use SwooleCli\Library;

$homeDir = getenv('HOME');
$p = Preprocessor::getInstance();
$p->parseArguments($argc, $argv);

# PHP 默认版本
$version = '8.2.4';

if ($p->getInputOption('with-php-version')) {
    $subject = $p->getInputOption('with-php-version');
    $pattern = '/(\d{1,2})\.\d{1,2}\.\d{1,2}/';
    if (preg_match($pattern, $subject, $match)) {
        if (intval($match[1]) >= 8) {
            $version = $match[0];
        }
    }
}

define('BUILD_PHP_VERSION', $version);

// Compile directly on the host machine, not in the docker container
if ($p->getInputOption('without-docker') || ($p->getOsType() == 'macos')) {
    $p->setWorkDir(__DIR__);
    $p->setBuildDir(__DIR__ . '/thirdparty');
}

// Sync code from php-src
//设置 PHP 源码所在目录
$p->setPhpSrcDir($p->getWorkDir() . '/php-src');

//设置PHP 安装目录
define("BUILD_PHP_INSTALL_PREFIX", $p->getWorkDir() . '/bin/php-' . BUILD_PHP_VERSION);

if ($p->getInputOption('with-global-prefix')) {
    $p->setGlobalPrefix($p->getInputOption('with-global-prefix'));
}

$build_type = $p->getInputOption('with-build-type');
if (!in_array($build_type, ['dev', 'debug'])) {
    $build_type = 'release';
}
define('PHP_CLI_BUILD_TYPE', $build_type);
define('PHP_CLI_GLOBAL_PREFIX', $p->getGlobalPrefix());

if ($p->getOsType() == 'macos') {
    $p->setExtraLdflags('-undefined dynamic_lookup');
    if (is_file('/usr/local/opt/llvm/bin/ld64.lld')) {
        $p->withBinPath('/usr/local/opt/llvm/bin')->setLinker('ld64.lld');
    }
}

$p->setExtraCflags('-Os');


// Generate make.sh
$p->execute();

function install_libraries(Preprocessor $p): void
{
    $php_install_prefix = BUILD_PHP_INSTALL_PREFIX;
    $php_src = $p->getPhpSrcDir();
    $build_dir = $p->getBuildDir();
    $p->addLibrary(
        (new Library('php_src'))
            ->withUrl('https://github.com/php/php-src/archive/refs/tags/php-' . BUILD_PHP_VERSION . '.tar.gz')
            ->withHomePage('https://www.php.net/')
            ->withLicense('https://github.com/php/php-src/blob/master/LICENSE', Library::LICENSE_PHP)
            ->withPrefix($php_install_prefix)
            ->withCleanBuildDirectory()
            ->withBuildScript(
                <<<EOF
                cd ..
                if test -d {$php_src} ; then
                    rm -rf {$php_src}
                fi
                cp -rf php_src {$php_src}
                cd {$build_dir}/php_src
EOF
            )
    );
}

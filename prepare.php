<?php

require __DIR__ . '/vendor/autoload.php';

use SwooleCli\Preprocessor;
use SwooleCli\Library;


$homeDir = getenv('HOME');
$p = Preprocessor::getInstance();
$p->parseArguments($argc, $argv);

$buildType = $p->getBuildType();
if ($p->getInputOption('with-build-type')) {
    $buildType = $p->getInputOption('with-build-type');
    $p->setBuildType($buildType);
}

# clean
# clean old make.sh
$p->cleanFile(__DIR__ . '/make.sh');
$p->cleanFile(__DIR__ . '/make-install-deps.sh');
$p->cleanFile(__DIR__ . '/make-env.sh');
$p->cleanFile(__DIR__ . '/make-export-variables.sh');
$p->cleanFile(__DIR__ . '/make-download-box.sh');
$p->cleanFile(__DIR__ . '/cppflags.log');
$p->cleanFile(__DIR__ . '/ldflags.log');
$p->cleanFile(__DIR__ . '/libs.log');
$p->cleanFile(__DIR__ . '/configure.backup');


# PHP 默认版本 （此文件配置 /sapi/PHP-VERSION.conf 在 build_native_php分支 和 衍生分支 无效）
$php_version = '8.2.13';
$php_version_id = '802013';
$php_version_tag = 'php-8.2.13';

if ($p->getInputOption('with-php-version')) {
    $subject = $p->getInputOption('with-php-version');
    $pattern = '/(\d{1,2})\.(\d{1,2})\.(\d{1,})\w*/';
    if (preg_match($pattern, $subject, $match)) {
        if (intval($match[1]) >= 8 && intval($match[2]) >= 1) {
            $php_version = $match[0];
            $php_version_id =
                str_pad($match[1], 2, '0') .
                str_pad($match[2], 2, '0') .
                sprintf('%02d', $match[3]);
            $php_version_tag = 'php-' . $match[0];
        } else {
            echo <<<EOF

    extra support PHP8.0  PHP7.4  PHP7.3  PHP8.2-micro

    php-8-micro:  (https://github.com/dixyes/phpmicro.git）

        git clone -b build_native_php_sfx_micro  https://github.com/jingjingxyk/swoole-cli/

    php-8.0

        git clone -b build_php_8.0  https://github.com/jingjingxyk/swoole-cli/

    php-7.4:

        git clone -b build_php_7.4  https://github.com/jingjingxyk/swoole-cli/

    php-7.3:

        git clone -b build_php_7.3  https://github.com/jingjingxyk/swoole-cli/

EOF;
            echo PHP_EOL;
            die;
        }
        echo PHP_EOL;
    }
}

define('BUILD_PHP_VERSION', $php_version);
define('BUILD_PHP_VERSION_ID', intval($php_version_id));
define('BUILD_PHP_VERSION_TAG', $php_version_tag);
define('BUILD_CUSTOM_PHP_VERSION_ID', intval(substr($php_version_id, 0, 4))); //取主版本号和次版本号

echo "PHP_VERSION: " . BUILD_PHP_VERSION . PHP_EOL;
echo "PHP_VERSION_ID: " . BUILD_PHP_VERSION_ID . PHP_EOL;
echo "PHP_VERSION_TAG: " . BUILD_PHP_VERSION_TAG . PHP_EOL;
echo "CUSTOM_PHP_VERSION_ID: " . BUILD_CUSTOM_PHP_VERSION_ID . PHP_EOL;
echo PHP_EOL;

// Sync code from php-src
$p->setPhpSrcDir($p->getWorkDir() . '/var/php-' . BUILD_PHP_VERSION);

// Compile directly on the host machine, not in the docker container
if ($p->getInputOption('without-docker') || ($p->isMacos())) {
    $p->setWorkDir(__DIR__);
    $p->setBuildDir(__DIR__ . '/thirdparty');
}

$p->setRootDir(__DIR__);

//设置 PHP 源码所在目录 (构建时将进入此目录进行构建)
if ($p->getInputOption('with-php-src')) {
    $p->setPhpSrcDir($p->getInputOption('with-php-src'));
} else {
    $p->setPhpSrcDir($p->getRootDir() . '/thirdparty/php-src');
}

//设置PHP 安装目录
define("BUILD_PHP_INSTALL_PREFIX", $p->getRootDir() . '/bin/php-' . BUILD_PHP_VERSION);


if ($p->getInputOption('with-override-default-enabled-ext')) {
    $p->setExtEnabled([]);
}


if ($p->getInputOption('with-global-prefix')) {
    $p->setGlobalPrefix($p->getInputOption('with-global-prefix'));
}

$buildType = $p->getBuildType();
if ($p->getInputOption('with-build-type')) {
    $buildType = $p->getInputOption('with-build-type');
    $p->setBuildType($buildType);
}
define('PHP_CLI_BUILD_TYPE', $buildType);
define('PHP_CLI_GLOBAL_PREFIX', $p->getGlobalPrefix());


if ($p->getInputOption('with-parallel-jobs')) {
    $p->setMaxJob(intval($p->getInputOption('with-parallel-jobs')));
}


if ($p->getInputOption('with-http-proxy')) {
    $http_proxy = $p->getInputOption('with-http-proxy');
    $proxyConfig = <<<EOF
export HTTP_PROXY={$http_proxy}
export HTTPS_PROXY={$http_proxy}
EOF;
    $proxyConfig .= PHP_EOL;
    $proxyConfig .= <<<'EOF'
NO_PROXY="127.0.0.0/8,10.0.0.0/8,100.64.0.0/10,172.16.0.0/12,192.168.0.0/16"
NO_PROXY="${NO_PROXY},::1/128,fe80::/10,fd00::/8,ff00::/8"
NO_PROXY="${NO_PROXY},.aliyuncs.com,.aliyun.com,.tencent.com"
NO_PROXY="${NO_PROXY},.tsinghua.edu.cn,.ustc.edu.cn,.npmmirror.com"
NO_PROXY="${NO_PROXY},ftpmirror.gnu.org"
NO_PROXY="${NO_PROXY},gitee.com,gitcode.com"
NO_PROXY="${NO_PROXY},.myqcloud.com,.swoole.com"
NO_PROXY="${NO_PROXY},dl-cdn.alpinelinux.org"
NO_PROXY="${NO_PROXY},deb.debian.org,security.debian.org"
NO_PROXY="${NO_PROXY},archive.ubuntu.com,security.ubuntu.com"
NO_PROXY="${NO_PROXY},pypi.python.org,bootstrap.pypa.io"
export NO_PROXY="${NO_PROXY},localhost"


EOF;
    $p->setProxyConfig($proxyConfig, $http_proxy);
}


if ($p->isMacos()) {
    $p->setExtraLdflags('-undefined dynamic_lookup');
    if (is_file('/usr/local/opt/llvm/bin/ld64.lld')) {
        $p->withBinPath('/usr/local/opt/llvm/bin')
            ->withBinPath('/usr/local/opt/flex/bin')
            ->withBinPath('/usr/local/opt/bison/bin')
            ->withBinPath('/usr/local/opt/libtool/bin')
            ->withBinPath('/usr/local/opt/m4/bin')
            ->withBinPath('/usr/local/opt/automake/bin/')
            ->withBinPath('/usr/local/opt/autoconf/bin/')
            ->setLinker('ld64.lld');
    } elseif (is_file('/opt/homebrew/opt/llvm/bin/ld64.lld')) { //兼容 macos arm64
        $p->withBinPath('/opt/homebrew/opt/llvm/bin/')
            ->withBinPath('/opt/homebrew/opt/flex/bin')
            ->withBinPath('/opt/homebrew/opt/bison/bin')
            ->withBinPath('/opt/homebrew/opt/libtool/bin')
            ->withBinPath('/opt/homebrew/opt/m4/bin')
            ->withBinPath('/opt/homebrew/opt/automake/bin/')
            ->withBinPath('/opt/homebrew/opt/autoconf/bin/')
            ->setLinker('ld64.lld');
    } else {
        $p->setLinker('lld');
    }
    $p->setLogicalProcessors('$(sysctl -n hw.ncpu)');
} else {
    $p->setLinker('ld.lld');
    $p->setLogicalProcessors('$(nproc 2> /dev/null)');
}


$c_compiler = $p->getInputOption('with-c-compiler');
if ($c_compiler == 'gcc') {
    $p->set_C_COMPILER('gcc');
    $p->set_CXX_COMPILER('g++');
    $p->setLinker('ld');
}


$p->setExtraCflags(' -Os');


// Generate make.sh
$p->execute();


function install_libraries(Preprocessor $p): void
{
    $p->loadDependentLibrary('php');
}

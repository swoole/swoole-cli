<?php

require __DIR__ . '/vendor/autoload.php';

use SwooleCli\Preprocessor;
use SwooleCli\Library;

# clean old make.sh
if (file_exists(__DIR__ . '/make.sh')) {
    unlink(__DIR__ . '/make.sh');
    unlink(__DIR__ . '/make-install-deps.sh');
    unlink(__DIR__ . '/make-env.sh');
    unlink(__DIR__ . '/make-export-variables.sh');
}

if (file_exists(__DIR__ . '/make-download-box.sh')) {
    unlink(__DIR__ . '/make-download-box.sh');
}

$homeDir = getenv('HOME');
$p = Preprocessor::getInstance();
$p->parseArguments($argc, $argv);

# PHP 默认版本
$php_version = '8.2.7';
$php_version_id = 802007;
$php_version_tag = 'php-8.2.7';

if ($p->getInputOption('with-php-version')) {
    $subject = $p->getInputOption('with-php-version');
    $pattern = '/(\d{1,2})\.(\d{1,2})\.(\d{1,})\w*/';
    if (preg_match($pattern, $subject, $match)) {
        $php_version = $match[0];
        $php_version_id =
            str_pad($match[1], 2, '0') .
            str_pad($match[2], 2, '0') .
            sprintf('%02d', $match[3]);
        $php_version_tag = 'php-' . $match[0];

        echo PHP_EOL;
    }
}

define('BUILD_PHP_VERSION', $php_version);
define('BUILD_PHP_VERSION_ID', intval($php_version_id));
define('BUILD_PHP_VERSION_TAG', $php_version_tag);
define('BUILD_CUSTOM_PHP_VERSION_ID', intval(substr($php_version_id, 0, 4))); //取主版本号和次版本号


// Compile directly on the host machine, not in the docker container
if ($p->getInputOption('without-docker') || ($p->getOsType() == 'macos')) {
    $p->setWorkDir(__DIR__);
    $p->setBuildDir(__DIR__ . '/thirdparty');
}

$p->setRootDir(__DIR__);

// Sync code from php-src
//设置 PHP 源码所在目录
$p->setPhpSrcDir($p->getRootDir() . '/php-src');

//设置PHP 安装目录
define("BUILD_PHP_INSTALL_PREFIX", $p->getRootDir() . '/bin/php-' . BUILD_PHP_VERSION);


if ($p->getInputOption('with-global-prefix')) {
    $p->setGlobalPrefix($p->getInputOption('with-global-prefix'));
}


//release 版本，屏蔽这两个函数，使其不生效
// ->withCleanBuildDirectory()
// ->withCleanPreInstallDirectory($prefix)
//SWOOLE_CLI_SKIP_DEPEND_DOWNLOAD
//SWOOLE_CLI_BUILD_TYPE=release


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
export NO_PROXY="127.0.0.0/8,10.0.0.0/8,100.64.0.0/10,172.16.0.0/12,192.168.0.0/16,198.18.0.0/15,169.254.0.0/16"
export NO_PROXY="\${NO_PROXY},127.0.0.1,localhost"
export NO_PROXY="\${NO_PROXY},.aliyuncs.com,.aliyun.com"
export NO_PROXY="\${NO_PROXY},.tsinghua.edu.cn,.ustc.edu.cn,.npmmirror.com"
export NO_PROXY="\${NO_PROXY},dl-cdn.alpinelinux.org,deb.debian.org,security.debian.org"
export NO_PROXY="\${NO_PROXY},archive.ubuntu.com,security.ubuntu.com"
export NO_PROXY="\${NO_PROXY},pypi.python.org,bootstrap.pypa.io"


EOF;
    $p->setProxyConfig($proxyConfig, $http_proxy);
}

if ($p->getInputOption('with-install-library-cached')) {
    $p->setInstallLibraryCached(true);
}


if ($p->getOsType() == 'macos') {
    $p->setLogicalProcessors('$(sysctl -n hw.ncpu)');
} else {
    $p->setLogicalProcessors('$(nproc 2> /dev/null)');
}

if ($p->getOsType() == 'macos') {
    // -lintl -Wl,-framework -Wl,CoreFoundation
    //$p->setExtraLdflags('-framework CoreFoundation -framework SystemConfiguration -undefined dynamic_lookup');
    $p->setExtraLdflags('-undefined dynamic_lookup');
    $p->setLinker('ld');
    if (is_file('/usr/local/opt/llvm/bin/ld64.lld')) {
        $p->withBinPath('/usr/local/opt/llvm/bin')->setLinker('ld64.lld');
    }
} else {
    $p->setLinker('ld.lld');
}


if ($p->getInputOption('with-c-compiler')) {
    $c_compiler = $p->getInputOption('with-c-compiler');
    if ($c_compiler == 'gcc') {
        $p->set_C_COMPILER('gcc');
        $p->set_CXX_COMPILER('g++');
        $p->setLinker('ld');
    }
}

# 设置CPU核数 ; 获取CPU核数，用于 make -j $(nproc)
# $p->setMaxJob(`nproc 2> /dev/null || sysctl -n hw.ncpu`); // nproc on macos ；
# `grep "processor" /proc/cpuinfo | sort -u | wc -l`

if ($p->getOsType() == 'macos') {
    $p->withPreInstallCommand(
        'macos',
        <<<'EOF'
export PATH=/opt/homebrew/bin/:/usr/local/bin/:$PATH

export HOMEBREW_INSTALL_FROM_API=1
export HOMEBREW_NO_ANALYTICS=1
export HOMEBREW_NO_AUTO_UPDATE=1
export PIPENV_PYPI_MIRROR=https://pypi.python.org/simple
EOF
    );

    if ($p->getInputOption('with-os-repository-mirror')) {
        $p->withPreInstallCommand(
            'macos',
            <<<EOF
export HOMEBREW_API_DOMAIN="https://mirrors.ustc.edu.cn/homebrew-bottles/api"
export HOMEBREW_BREW_GIT_REMOTE="https://mirrors.ustc.edu.cn/brew.git"
export HOMEBREW_CORE_GIT_REMOTE="https://mirrors.ustc.edu.cn/homebrew-core.git"
export HOMEBREW_BOTTLE_DOMAIN="https://mirrors.ustc.edu.cn/homebrew-bottles"

export HOMEBREW_PIP_INDEX_URL="https://pypi.tuna.tsinghua.edu.cn/simple"
export HOMEBREW_API_DOMAIN="https://mirrors.tuna.tsinghua.edu.cn/homebrew-bottles/api"
export HOMEBREW_BREW_GIT_REMOTE="https://mirrors.tuna.tsinghua.edu.cn/git/homebrew/brew.git"

export PIPENV_PYPI_MIRROR=https://pypi.tuna.tsinghua.edu.cn/simple

# pip3 config set global.index-url https://pypi.tuna.tsinghua.edu.cn/simple
# pip3 config set global.index-url https://pypi.python.org/simple


mkdir -p ~/.pip
cat > ~/.pip/pip.conf <<===EOF===
[global]
index-url = https://pypi.tuna.tsinghua.edu.cn/simple
[install]
trusted-host = https://pypi.tuna.tsinghua.edu.cn
===EOF===
EOF
        );
    }

    $p->withPreInstallCommand(
        'macos',
        <<<'EOF'
brew=$(which brew  | wc -l)
if test $brew -eq 1 ;then
{
    meson=$(which meson  | wc -l)
    if test $meson -ne  1 ;then
    {
        brew install ninja  python3 gn zip unzip 7zip lzip go flex
        # pip3 install meson virtualenv -i https://pypi.tuna.tsinghua.edu.cn/simple
        # pip3 config set global.index-url https://pypi.tuna.tsinghua.edu.cn/simple
        pip3 install meson virtualenv
    }
    fi
}
fi
EOF
    );
}


if ($p->getOsType() == 'linux') {
    $p->withPreInstallCommand(
        'debian',
        <<<'EOF'
test -f /etc/apt/apt.conf.d/proxy.conf && rm -rf /etc/apt/apt.conf.d/proxy.conf
export PIPENV_PYPI_MIRROR=https://pypi.python.org/simple

EOF
    );

    if ($p->getInputOption('with-os-repository-mirror')) {
        $p->withPreInstallCommand(
            'debian',
            <<<'EOF'
export PIPENV_PYPI_MIRROR=https://pypi.tuna.tsinghua.edu.cn/simple

mkdir -p ~/.pip
cat > ~/.pip/pip.conf <<===EOF===
[global]
index-url = https://pypi.tuna.tsinghua.edu.cn/simple
[install]
trusted-host = https://pypi.tuna.tsinghua.edu.cn
===EOF===

EOF
        );
    }

    $p->withPreInstallCommand(
        'alpine',
        <<<'EOF'
        meson=$(which meson | wc -l )
        if test $meson -ne 1 ;then
        {
             cd ${__CURRENT_DIR__}
             # bash sapi/quickstart/linux/alpine-init.sh --mirror china
             apk add ninja python3 py3-pip gn zip unzip p7zip lzip  go flex
             apk add yasm nasm
             # pip3 config set global.index-url https://pypi.tuna.tsinghua.edu.cn/simple
             # pip3 config set global.index-url https://pypi.python.org/simple
             pip3 install meson virtualenv pipenv
             # git config --global --add safe.directory /work
        }
        fi

EOF
    );
    $p->withPreInstallCommand(
        'ubuntu',
        <<<'EOF'
            meson=$(which meson | wc -l )
            if test $meson -ne 1 ;then
            {
                # bash sapi/quickstart/linux/debian-init.sh --mirror china
                apt install -y python3 python3-pip ninja-build  gn zip unzip p7zip lzip  golang flex
                apt install -y yasm nasm
                # pip3 config set global.index-url https://pypi.tuna.tsinghua.edu.cn/simple
                # pip3 install meson virtualenv pipenv
                apt  install -y  meson
                # pip3 install virtualenv pipenv
                # git config --global --add safe.directory /work
            }
            fi

             # sed -i "s@mirrors.ustc.edu.cn@mirrors.tuna.tsinghua.edu.cn@g" /etc/apt/sources.list
             # sed -i 's@//.*archive.ubuntu.com@//mirrors.ustc.edu.cn@g' /etc/apt/sources.list

EOF
    );

    $p->withPreInstallCommand(
        'ubuntu',
        <<<'EOF'
           meson=$(which meson | wc -l )
            if test $meson -ne 1 ;then
            {
                # bash sapi/quickstart/linux/debian-init.sh --mirror china
                apt install -y python3 python3-pip ninja-build  gn zip unzip p7zip lzip  golang flex
                apt install -y yasm nasm
                # pip3 config set global.index-url https://pypi.tuna.tsinghua.edu.cn/simple
                # pip3 install meson virtualenv pipenv
                apt  install -y  meson
                # pip3 install virtualenv pipenv
                # git config --global --add safe.directory /work
            }
            fi

             # sed -i "s@mirrors.ustc.edu.cn@mirrors.tuna.tsinghua.edu.cn@g" /etc/apt/sources.list
             # sed -i 's@//.*archive.ubuntu.com@//mirrors.ustc.edu.cn@g' /etc/apt/sources.list

EOF
    );
}

# GN=generate-ninja

$header = <<<'EOF'
#!/use/bin/env bash
export CPU_NUMS=`nproc 2> /dev/null || sysctl -n hw.ncpu`
# `grep "processor" /proc/cpuinfo | sort -u | wc -l`
export __CURRENT_DIR__=$(cd "$(dirname $0)";pwd)

EOF;


#$p->setExtraCflags('-fno-ident -Os');

$p->setExtraCflags(' -Os');


// Generate make.sh
$p->execute();


function install_libraries(Preprocessor $p): void
{
    //$p->loadDependentLibrary('php_src');
}

<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    if ($p->getOsType() == 'macos') {
    } elseif ($p->getOsType() == 'linux') {
        $current_dir = $p->getWorkDir(); //SWOOLE_CLI_WITH_OS_MIRROR
        $with_os_mirror = SWOOLE_CLI_WITH_OS_MIRROR;
        $cmd = <<<EOF
        cd {$current_dir}
            export SWOOLE_CLI_WITH_OS_MIRROR=$with_os_mirror;
            if test -f /etc/os-release; then
            {
                OS_RELEASE=$(cat /etc/os-release | grep "^ID=" | sed 's/ID=//g')
                if test \$OS_RELEASE = alpine  ;then
                {
                    export PIPENV_PYPI_MIRROR=https://pypi.python.org/simple

                    if [[ "\${SWOOLE_CLI_WITH_OS_MIRROR}" -eq 1 ]] ; then
                    {
                        export RUSTUP_DIST_SERVER=https://mirrors.ustc.edu.cn/rust-static
                        export RUSTUP_UPDATE_ROOT=https://mirrors.ustc.edu.cn/rust-static/rustup
                        export PIPENV_PYPI_MIRROR=https://pypi.tuna.tsinghua.edu.cn/simple

                        mkdir -p /root/.cargo/
                        cat > /root/.cargo/config <<'___EOF___'
[source.crates-io]
replace-with = 'ustc'

[source.ustc]
registry = "git://mirrors.ustc.edu.cn/crates.io-index"
___EOF___
                        rm -rf /root/.cargo/config
                        bash sapi/quickstart/linux/alpine-init.sh --mirror china
                    }
                    fi
                    meson=$(which cargo-c | wc -l )
                    if test \$meson -ne 1 ;then
                    {

                        apk update
                        apk add ninja python3 py3-pip  nasm
                        # pip3
                        # curl -o get-pip.py  https://bootstrap.pypa.io/get-pip.py # install pip3

                        if [[ "\${SWOOLE_CLI_WITH_OS_MIRROR}" -eq 1 ]] ; then
                        {
                            pip3 config set global.index-url https://pypi.tuna.tsinghua.edu.cn/simple
                        }
                        fi
                        pip3 install meson virtualenv pipenv
                        # apk add cargo

                        # /root/.cargo/bin
                        export RUSTUP_HOME=/root/.rustup
                        export CARGO_HOME=/root/.cargo
                        {$p->getProxyConfig()}
                        # curl https://sh.rustup.rs -sSf | bash -s -- --help
                        curl https://sh.rustup.rs -sSf | bash -s -- --quiet
                        export PATH=\$PATH:/root/.cargo/bin
                        rustc -V
                        cargo -V
                        # cargo --list

                        cargo install cargo-c

                        # git config --global --add safe.directory /work
                    }
                    fi
                }
                fi
            }
            fi





EOF;
    }


    $lib = new Library('common');
    $lib->withHomePage('https://github.com/jingjingxyk/swoole-cli/')
        ->withLicense('https://github.com/jingjingxyk/swoole-cli//LICENSE', Library::LICENSE_APACHE2)
        ->withManual('https://github.com/jingjingxyk/swoole-cli')
        ->withUrl('https://github.com/quictls/openssl/archive/refs/tags/openssl-3.0.8-quic1.tar.gz')
        ->withBuildLibraryCached(false)
        ->withBuildScript($cmd)
        ->withLdflags('')
        ->withPkgConfig('');

    $p->addLibrary($lib);
};

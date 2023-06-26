<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $ceph_prefix = CEPH_PREFIX;
    $lib = new Library('ceph');
    $lib->withHomePage('https://ceph.io/')
        ->withLicense('https://github.com/ceph/ceph/blob/main/COPYING-LGPL3', Library::LICENSE_LGPL)
        ->withManual('https://github.com/ceph/ceph')
        ->withFile('ceph-latest.tar.gz')
        ->withBuildCached(true)
        ->withDownloadScript(
            'ceph',
            <<<EOF
                # git clone -b main  --depth=1 --recursive https://github.com/ceph/ceph.git
                # for docs
                git clone -b main  --depth=1  https://github.com/ceph/ceph.git
EOF
        )
        ->withPrefix($ceph_prefix)
        ->withBuildScript(
            <<<EOF

            #  docker run --rm --name demo  -ti --init -v $(pwd):/work/ -p 7010:7010 debian:11 /bin/bash
            #  docker run --rm --name demo  -ti --init -v $(pwd):/work/ -p 7010:7010 ubuntu:22.04 /bin/bash
            #  bash sapi/quickstart/linux/debian-init.sh --mirror china
            #  bash setup-php-runtime.sh --mirror china
            #  export PATH="/work/bin/runtime:\$PATH"
            #  alias php='php -c /work/bin/runtime/php.ini'

            export PKG_CONFIG_PATH=\${SYSTEM_ORIGIN_PKG_CONFIG_PATH}
            export PATH=\${SYSTEM_ORIGIN_PATH}

            OS_RELEASE=$(cat /etc/os-release | grep "^ID=" | sed 's/ID=//g')
            SUPPORT_OS=0
            if [ -f /etc/os-release ] ; then
                case \$OS_RELEASE in
                'debian')
                  SUPPORT_OS=1
                  ;;
                'ubuntu')
                  SUPPORT_OS=1
                  sed -i 's@//.*archive.ubuntu.com@//mirrors.ustc.edu.cn@g' /etc/apt/sources.list
                  ;;
                'alpine')
                  SUPPORT_OS=1
                  ;;
                *)
                  ;;
                esac
            fi
            if test \$SUPPORT_OS -ne 1 ;then
                echo 'no support os'
                exit 0
            fi

            mkdir -p ~/.pip
cat > ~/.pip/pip.conf <<===EOF===
[global]
index-url = https://pypi.tuna.tsinghua.edu.cn/simple
[install]
trusted-host = https://pypi.tuna.tsinghua.edu.cn
===EOF===

            # bash ./install-deps.sh

            bash src/cephadm/build.sh cephadm

            apt-get install -y `cat doc_deps.deb.txt`
            pip3 install -r admin/doc-requirements.txt
            pip3 install  -r admin/doc-python-common-requirements.txt
            admin/build-doc

            export PKG_CONFIG_PATH=\${SWOOLE_CLI_PKG_CONFIG_PATH}
            export PATH=\${SWOOLE_CLI_PATH}
EOF
        )
        ->disableDefaultLdflags()
        ->disablePkgName()
        ->disableDefaultPkgConfig()
        ->withSkipBuildLicense();

    $p->addLibrary($lib);
};

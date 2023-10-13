<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $ceph_prefix = CEPH_PREFIX;
    $lib = new Library('ceph');
    $lib->withHomePage('https://ceph.io/')
        ->withLicense('https://github.com/ceph/ceph/blob/main/COPYING-LGPL3', Library::LICENSE_LGPL)
        ->withManual('https://github.com/ceph/ceph')
        //->withAutoUpdateFile()
        //->withBuildLibraryCached(false)
        ->withFile('ceph-latest.tar.gz')
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




test -f /etc/apt/apt.conf.d/proxy.conf && rm -rf /etc/apt/apt.conf.d/proxy.conf

mkdir -p /etc/apt/apt.conf.d/

cat > /etc/apt/apt.conf.d/proxy.conf <<'--EOF--'
Acquire::http::Proxy "{$p->getHttpProxy()}";
Acquire::https::Proxy "{$p->getHttpProxy()}";

--EOF--


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
                  SUPPORT_OS=0
                  ;;
                'ubuntu')
                  SUPPORT_OS=1
                  ;;
                'alpine')
                  SUPPORT_OS=0
                  ;;
                *)
                  ;;
                esac
            fi
            if test \$SUPPORT_OS -ne 1 ;then
                echo 'no support OS'
                exit 3
            fi
            # 仅支持 ubuntu

            if [ -f build-env-ok ] ; then
                bash src/cephadm/build.sh cephadm
                admin/build-doc
            else
                bash ./install-deps.sh

                bash src/cephadm/build.sh cephadm

                apt-get install -y `cat doc_deps.deb.txt`
                pip3 install -r admin/doc-requirements.txt
                pip3 install  -r admin/doc-python-common-requirements.txt
                admin/build-doc

                touch build-env-ok
            fi
            mkdir -p {$ceph_prefix}/ceph/
            touch {$ceph_prefix}/ceph/.completed
            export PKG_CONFIG_PATH=\${SWOOLE_CLI_PKG_CONFIG_PATH}
            export PATH=\${SWOOLE_CLI_PATH}
            test -f /etc/apt/apt.conf.d/proxy.conf && rm -rf /etc/apt/apt.conf.d/proxy.conf
EOF
        )
        ->disableDefaultLdflags()
        ->disableDefaultPkgConfig()
    ;

    $p->addLibrary($lib);
};
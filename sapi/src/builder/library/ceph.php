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
        ->withInstallCached(false)
        //->withBuildCached(false)
        ->withSystemEnvPath()
        ->withBuildLibraryHttpProxy()
        ->withBuildScript(
            <<<EOF

test -f /etc/apt/apt.conf.d/proxy.conf && rm -rf /etc/apt/apt.conf.d/proxy.conf

mkdir -p /etc/apt/apt.conf.d/

cat > /etc/apt/apt.conf.d/proxy.conf <<'--CEPH-EOF--'
Acquire::http::Proxy  "{$p->getHttpProxy()}";
Acquire::https::Proxy "{$p->getHttpProxy()}";

--CEPH-EOF--


            OS_RELEASE=$(cat /etc/os-release | grep "^ID=" | sed 's/ID=//g')

            if [ "\${OS_RELEASE}" != "ubuntu" ] ; then
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
                pip3 install  -r admin/doc-requirements.txt
                pip3 install  -r admin/doc-python-common-requirements.txt
                admin/build-doc

                touch build-env-ok
            fi

            test -f /etc/apt/apt.conf.d/proxy.conf && rm -rf /etc/apt/apt.conf.d/proxy.conf
EOF
        )
        ->disableDefaultLdflags()
        ->disableDefaultPkgConfig()
    ;

    $p->addLibrary($lib);
};

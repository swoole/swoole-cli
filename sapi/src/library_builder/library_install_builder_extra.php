<?php


use SwooleCli\Library;
use SwooleCli\Preprocessor;

function install_ovs(Preprocessor $p)
{

}

function install_ovn(Preprocessor $p)
{

}

function install_nginx($p)
{

}

function install_dpdk(Preprocessor $p): void
{

}

function install_xdp(Preprocessor $p): void
{
    $xdp_prefix = '/usr/xdp';
    $p->addLibrary(
        (new Library('xdp'))
            ->withHomePage('https://github.com/xdp-project/xdp-tools.git')
            ->withLicense('https://github.com/xdp-project/xdp-tools/blob/master/LICENSE', Library::LICENSE_BSD)
            ->withUrl('https://github.com/xdp-project/xdp-tools/archive/refs/tags/v1.3.1.tar.gz')
            ->withFile('xdp-v1.3.1.tar.gz')
            ->withFile('')
            ->withManual('https://github.com/xdp-project/xdp-tutorial')
            ->withDownloadScript(
                'xdp-tutorial',
                <<<EOF
                    https://github.com/xdp-project/xdp-tutorial.git
EOF
            )
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF
                apk add llvm bpftool
EOF
            )
            ->withBinPath($xdp_prefix . '/bin/')
            ->withSkipBuildInstall()
    );
}

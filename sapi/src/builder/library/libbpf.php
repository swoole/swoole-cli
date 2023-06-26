<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libbpf_prefix = LIBBPF_PREFIX;
    $p->addLibrary(
        (new Library('libbpf'))
            ->withHomePage('https://github.com/libbpf/libbpf.git')
            ->withLicense('https://github.com/libbpf/libbpf/blob/master/LICENSE.BSD-2-Clause', Library::LICENSE_LGPL)
            ->withUrl('https://github.com/libbpf/libbpf/archive/refs/tags/v1.1.0.tar.gz')
            ->withFile('libbpf-v1.1.0.tar.gz')
            ->withManual('https://libbpf.readthedocs.io/en/latest/libbpf_build.html')
            ->withPrefix($libbpf_prefix)
            ->withCleanBuildDirectory()
            ->withConfigure(
                <<<EOF
                cd src
                mkdir -p build
EOF
            )
            ->withMakeOptions('BUILD_STATIC_ONLY=y OBJDIR=build')
            ->withMakeInstallOptions('DESTDIR='.$libbpf_prefix)
            ->withPkgName('libbpf')
            ->withDependentLibraries('zlib','libelf')
    );
};

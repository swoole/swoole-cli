<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libbpf_prefix = LIBBPF_PREFIX;
    $libelf_prefix = LIBELF_PREFIX;
    $p->addLibrary(
        (new Library('libbpf'))
            ->withHomePage('https://github.com/libbpf/libbpf.git')
            ->withLicense('https://github.com/libbpf/libbpf/blob/master/LICENSE.BSD-2-Clause', Library::LICENSE_LGPL)
            ->withUrl('https://github.com/libbpf/libbpf/archive/refs/tags/v1.1.0.tar.gz')
            ->withFile('libbpf-v1.1.0.tar.gz')
            ->withManual('https://libbpf.readthedocs.io/en/latest/libbpf_build.html')
            ->withPrefix($libbpf_prefix)
            /*
            ->withBuildLibraryCached(false)
            */
            ->withPreInstallCommand(
                'debian',
                <<<EOF
            # apt install libelf-dev
EOF
            )
            ->withPreInstallCommand(
                'alpine',
                <<<EOF
            # apk add libelf-static elfutils-dev
EOF
            )
            ->withCleanPreInstallDirectory($libbpf_prefix)
            ->withBuildLibraryCached(false)
            ->withBuildScript(
                <<<EOF
                # xdg-open https://kernel.googlesource.com/pub/scm/linux/kernel/git/bpf/bpf-next

                # git://git.kernel.org/pub/scm/linux/kernel/git/bpf/bpf-next.git

                cd src
                mkdir -p build {$libbpf_prefix}
                set -ex

                BUILD_STATIC_ONLY=y OBJDIR=build  PREFIX={$libbpf_prefix} LIBDIR={$libbpf_prefix}/lib INCLUDEDIR={$libbpf_prefix}/include make -j {$p->maxJob}
                BUILD_STATIC_ONLY=y OBJDIR=build  PREFIX={$libbpf_prefix} LIBDIR={$libbpf_prefix}/lib INCLUDEDIR={$libbpf_prefix}/include make install
EOF
            )
            ->withPkgName('libbpf')
            ->withDependentLibraries('zlib', 'libelf')
    );
};

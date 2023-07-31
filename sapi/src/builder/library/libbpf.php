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
            ->withBuildLibraryCached(false)
            ->withCleanBuildDirectory()
            ->withPreInstallCommand('debian',
                <<<EOF
            apt install libelf-dev
EOF
            )
            ->withBuildScript(
                <<<EOF
                # xdg-open https://kernel.googlesource.com/pub/scm/linux/kernel/git/bpf/bpf-next

                # git://git.kernel.org/pub/scm/linux/kernel/git/bpf/bpf-next.git

                cd src
                mkdir -p build
                PACKAGES="  zlib" # libelf
                CPPFLAGS="$(pkg-config  --cflags-only-I  --static \$PACKAGES) " \
                LDFLAGS="$(pkg-config   --libs-only-L    --static \$PACKAGES) " \
                LIBS="$(pkg-config      --libs-only-l    --static \$PACKAGES) " \
                make -j {$p->maxJob} BUILD_STATIC_ONLY=y OBJDIR=build
                make install DESTDIR={$libbpf_prefix}
EOF
            )
            ->withScriptAfterInstall(
                <<<EOF
            rm -rf {$libbpf_prefix}/lib/*.so.*
            rm -rf {$libbpf_prefix}/lib/*.so
            rm -rf {$libbpf_prefix}/lib/*.dylib
EOF
            )
            ->withPkgName('libbpf')
            ->withDependentLibraries('zlib') //'libelf'
    );
};

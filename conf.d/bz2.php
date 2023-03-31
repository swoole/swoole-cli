<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    if ($p->getOsType() == 'macos') {
        $bison_prefix = BISON_PREFIX;
        $p->addLibrary(
            (new Library('bison'))
                ->withHomePage('https://www.gnu.org/software/bison/')
                ->withUrl('http://ftp.gnu.org/gnu/bison/bison-3.8.tar.gz')
                ->withLicense('https://www.gnu.org/licenses/gpl-3.0.html', Library::LICENSE_GPL)
                ->withManual('https://www.gnu.org/software/bison/manual/')
                ->withConfigure(
                    <<<EOF
                     ./configure --help
                     ./configure --prefix={$bison_prefix} 
EOF
                )
                ->withBinPath($bison_prefix . '/bin/')
        );
    }
    $bzip2_prefix = BZIP2_PREFIX;
    $p->addLibrary(
        (new Library('bzip2'))
            ->withUrl('https://sourceware.org/pub/bzip2/bzip2-1.0.8.tar.gz')
            ->withPrefix($bzip2_prefix)
            ->withMakeOptions('PREFIX=' . $bzip2_prefix)
            ->withMakeInstallOptions('PREFIX=' . $bzip2_prefix)
            ->withHomePage('https://www.sourceware.org/bzip2/')
            ->withLicense('https://www.sourceware.org/bzip2/', Library::LICENSE_BSD)
            ->withBinPath($bzip2_prefix . '/bin/')
    );
    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $bzip2_prefix . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $bzip2_prefix . '/lib');
    $p->withVariable('LIBS', '$LIBS -lbz2');
    $p->addExtension((new Extension('bz2'))->withOptions('--with-bz2=' . BZIP2_PREFIX)->depends('bzip2'));
};

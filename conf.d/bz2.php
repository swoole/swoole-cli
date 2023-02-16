<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addLibrary(
        (new Library('bzip2'))
            ->withUrl('https://sourceware.org/pub/bzip2/bzip2-1.0.8.tar.gz')
            ->withPrefix('/usr/bzip2')
            ->withMakeOptions('PREFIX=/usr/bzip2')
            ->withMakeInstallOptions('PREFIX=/usr/bzip2')
            ->withHomePage('https://www.sourceware.org/bzip2/')
            ->withLicense('https://www.sourceware.org/bzip2/', Library::LICENSE_BSD)
    );
    $p->addExtension((new Extension('bz2'))->withOptions('--with-bz2=/usr/bzip2')->depends('bzip2'));
};

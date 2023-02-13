<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addLibrary(
        (new Library('icu'))
            ->withUrl('https://github.com/unicode-org/icu/releases/download/release-60-3/icu4c-60_3-src.tgz')
            ->withConfigure('source/runConfigureICU Linux --prefix=/usr --enable-static --disable-shared')
            ->withPkgName('icu-i18n')
            ->withHomePage('https://icu.unicode.org/')
            ->withLicense('https://github.com/unicode-org/icu/blob/main/icu4c/LICENSE', Library::LICENSE_SPEC)
    );
    $p->addExtension((new Extension('intl'))->withOptions('--enable-intl')->depends('icu'));
};

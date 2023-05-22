<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $icu_prefix = ICU_PREFIX;
    $os = $p->getOsType() == 'macos' ? 'MacOSX' : 'Linux';
    $p->addLibrary(
        (new Library('icu'))
            ->withHomePage('https://icu.unicode.org/')
            ->withLicense('https://github.com/unicode-org/icu/blob/main/icu4c/LICENSE', Library::LICENSE_SPEC)
            ->withManual(
                'https://unicode-org.github.io/icu/userguide/icu_data/#:~:text=Building%20and%20Linking%20against%20ICU%20data'
            )
            ->withUrl('https://github.com/unicode-org/icu/releases/download/release-60-3/icu4c-60_3-src.tgz')
            ->withManual('https://unicode-org.github.io/icu/userguide/icu_data/#overview')
            ->withPrefix($icu_prefix)
            ->withConfigure(
                <<<EOF
             CPPFLAGS="-DU_CHARSET_IS_UTF8=1  -DU_USING_ICU_NAMESPACE=1  -DU_STATIC_IMPLEMENTATION=1" \
             source/runConfigureICU $os --prefix={$icu_prefix} \
             --enable-static=yes \
             --enable-shared=no \
             --with-data-packaging=static \
             --enable-release=yes \
             --enable-extras=yes \
             --enable-icuio=yes \
             --enable-dyload=no \
             --enable-tools=yes \
             --enable-tests=no \
             --enable-samples=no
EOF
            )
            ->withPkgName('icu-i18n')
            ->withPkgName('icu-io')
            ->withPkgName('icu-uc')
            ->withBinPath($icu_prefix . '/bin/:' . $icu_prefix . "/sbin")
    );
};

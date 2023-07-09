<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $pcre2_prefix = PCRE2_PREFIX;
    $p->addLibrary(
        (new Library('pcre2'))
            ->withHomePage('https://github.com/PCRE2Project/pcre2.git')
            ->withUrl('https://github.com/PCRE2Project/pcre2/releases/download/pcre2-10.42/pcre2-10.42.tar.gz')
            ->withDocumentation('https://pcre2project.github.io/pcre2/doc/html/index.html')
            ->withManual('https://github.com/PCRE2Project/pcre2.git')
            ->withLicense(
                'https://github.com/PCRE2Project/pcre2/blob/master/COPYING',
                Library::LICENSE_SPEC
            )
            ->withFile('pcre2-10.42.tar.gz')
            ->withPrefix($pcre2_prefix)
            ->withCleanBuildDirectory()
            ->withCleanPreInstallDirectory($pcre2_prefix)
            ->withConfigure(
                <<<EOF
                ./configure --help

                ./configure \
                --prefix=$pcre2_prefix \
                --enable-shared=no \
                --enable-static=yes \
                --enable-pcre2-16 \
                --enable-pcre2-32 \
                --enable-jit \
                --enable-unicode


 EOF
            )
            ->withBuildCached(false)
            ->withBinPath($pcre2_prefix . '/bin/')
            ->withPkgName("libpcre2-16")
            ->withPkgName("libpcre2-32")
            ->withPkgName("libpcre2-8")
            ->withPkgName("libpcre2-posix")
    );
};

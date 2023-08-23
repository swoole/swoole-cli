<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libmarisa_prefix = LIBMARISA_PREFIX;
    $lib = new Library('libmarisa');
    $lib->withHomePage('https://github.com/s-yata/marisa-trie.git')
        ->withLicense('https://github.com/s-yata/marisa-trie/blob/master/COPYING.md', Library::LICENSE_LGPL)
        ->withManual('https://github.com/s-yata/marisa-trie.git')
        ->withUrl('https://github.com/s-yata/marisa-trie/archive/refs/tags/v0.2.6.tar.gz')
        ->withFile('libmarisa-v0.2.6.tar.gz')
        ->withPrefix($libmarisa_prefix)
        ->withConfigure(
            <<<EOF
             autoreconf -i

            ./configure --help

            ./configure \
            --prefix={$libmarisa_prefix} \
            --enable-shared=no \
            --enable-static=yes \
            --enable-native-code

EOF
        )
        ->withPkgName('marisa')
        ->withBinPath($libmarisa_prefix . '/bin/');

    $p->addLibrary($lib);
};

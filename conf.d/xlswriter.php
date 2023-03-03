<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;
use SwooleCli\Library;

return function (Preprocessor $p) {
    $libxlsxwriter_prefix = LIBXLSXWRITER_PREFIX;
    $zlib_prefix =  ZLIB_PREFIX;
    $lib = new Library('libxlsxwriter');
    $lib->withHomePage('https://sourceforge.net/projects/mcrypt/files/Libmcrypt/')
        ->withLicense('https://github.com/jmcnamara/libxlsxwriter/blob/main/License.txt', Library::LICENSE_LGPL)
        ->withUrl('https://github.com/jmcnamara/libxlsxwriter/archive/refs/tags/RELEASE_1.1.5.tar.gz')
        ->withFile('libxlsxwriter-1.1.5.tar.gz')
        ->withPrefix($libxlsxwriter_prefix)
        ->withBuildScript(
            <<<EOF
            # 启用DBUILD_TESTS 需要安装python3 pytest
            mkdir build && cd build
            cmake .. -DCMAKE_INSTALL_PREFIX={$libxlsxwriter_prefix} \
            -DCMAKE_BUILD_TYPE=Release \
            -DZLIB_ROOT:STRING={$zlib_prefix} \
            -DBUILD_TESTS=OFF \
            -DBUILD_EXAMPLES=OFF \
            -DUSE_STANDARD_TMPFILE=ON \
            -DUSE_OPENSSL_MD5=ON \
            && \
            cmake --build . --config Release --target install
EOF
        )
        ->depends('zlib')
        ->withPkgName('xlsxwriter');

    $p->addExtension(
        (new Extension('xlswriter'))
            ->withOptions('--with-libxlsxwriter=' . LIBXLSXWRITER_PREFIX)
            ->withPeclVersion('1.5.2')
            ->withHomePage('https://github.com/viest/php-ext-xlswriter')
            ->withLicense('https://github.com/viest/php-ext-xlswriter/blob/master/LICENSE', Extension::LICENSE_BSD)
            ->depends('libxlsxwriter')
    );
};

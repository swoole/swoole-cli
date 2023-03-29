<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $libxslt_prefix = LIBXSLT_PREFIX;
    $libxml2_prefix = LIBXML2_PREFIX;
    $p->addLibrary(
        (new Library('libxslt'))
            ->withHomePage('https://gitlab.gnome.org/GNOME/libxslt/-/wikis/home')
            ->withUrl('https://gitlab.gnome.org/GNOME/libxslt/-/archive/v1.1.34/libxslt-v1.1.34.tar.gz')
            //https://download.gnome.org/sources/libxslt/1.1/
            ->withLicense('http://www.opensource.org/licenses/mit-license.html', Library::LICENSE_MIT)
            ->withPrefix($libxslt_prefix)
            ->withConfigure(
                <<<EOF
            ./autogen.sh
            ./configure --help
            CPPFLAGS="$(pkg-config  --cflags-only-I  --static libxml-2.0  )" \
            LDFLAGS="$(pkg-config --libs-only-L      --static libxml-2.0  )" \
            LIBS="$(pkg-config --libs-only-l         --static libxml-2.0  )" \
            ./configure \
            --prefix={$libxslt_prefix} \
            --enable-static=yes \
            --enable-shared=no \
            --with-libxml-libs-prefix={$libxml2_prefix} \
            --without-python \
            --without-crypto \
            --without-profiler \
            --without-plugins \
            --without-debugger
EOF
            )
            ->withPkgName('libexslt')
            ->withPkgName('libxslt')
            ->withBinPath($libxslt_prefix . '/bin/')
            ->depends('libxml2', 'libiconv')
    );
    $p->withExportVariable('XSL_CFLAGS', '$(pkg-config    --cflags --static libxslt)');
    $p->withExportVariable('XSL_LIBS', '$(pkg-config      --libs   --static libxslt)');
    $p->withExportVariable('EXSLT_CFLAGS', '$(pkg-config  --cflags --static libexslt)');
    $p->withExportVariable('EXSLT_LIBS', '$(pkg-config    --libs   --static libexslt)');
    $p->addExtension((new Extension('xsl'))->withOptions('--with-xsl')->depends('libxslt'));
};

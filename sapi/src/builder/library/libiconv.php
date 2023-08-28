<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libiconv_prefix = ICONV_PREFIX;
    $p->addLibrary(
        (new Library('libiconv'))
            ->withHomePage('https://www.gnu.org/software/libiconv/')
            ->withManual('https://www.gnu.org/software/libiconv/')
            ->withLicense('https://www.gnu.org/licenses/old-licenses/gpl-2.0.html', Library::LICENSE_GPL)
            ->withUrl('https://ftp.gnu.org/pub/gnu/libiconv/libiconv-1.16.tar.gz')
            ->withPrefix($libiconv_prefix)
            ->withConfigure('./configure --prefix=' . $libiconv_prefix . ' enable_static=yes enable_shared=no')
            ->withBinPath($libiconv_prefix . '/bin/')
            ->withScriptAfterInstall(
                <<<EOF
                mkdir -p {$libiconv_prefix}/lib/pkgconfig/
                cat > {$libiconv_prefix}/lib/pkgconfig/iconv.pc <<'__libiconv__EOF'
prefix={$libiconv_prefix}
exec_prefix=\${prefix}
libdir=\${exec_prefix}/lib
includedir=\${prefix}/include

Name: iconv
Description: iconv library
Version: 1.16

Requires:
Libs: -L\${libdir} -liconv
Cflags: -I\${includedir}

__libiconv__EOF
EOF
            )
            // ->withPkgName('iconv')
            ->withLdflags('-L' . $libiconv_prefix . '/lib')
            ->withPkgConfig('')
    );

    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $libiconv_prefix . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $libiconv_prefix . '/lib');

    $p->withVariable('LIBS', '$LIBS -liconv');
};

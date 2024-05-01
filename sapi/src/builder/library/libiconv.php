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
            ->withUrl('https://ftp.gnu.org/pub/gnu/libiconv/libiconv-1.17.tar.gz')
            ->withMirrorUrl('https://mirrors.tuna.tsinghua.edu.cn/gnu/libiconv/libiconv-1.17.tar.gz')
            ->withMirrorUrl('https://mirrors.ustc.edu.cn/gnu/libiconv/libiconv-1.17.tar.gz')
            ->withFileHash('md5', 'd718cd5a59438be666d1575855be72c3')
            ->withPrefix($libiconv_prefix)
            ->withConfigure('./configure --prefix=' . $libiconv_prefix . ' enable_static=yes enable_shared=no')
            ->withInstallCached(false)
            ->withScriptAfterInstall(
                <<<EOF
            mkdir -p {$libiconv_prefix}/lib/pkgconfig

            cat << '__example_PKGCONFIG_EOF__' > {$libiconv_prefix}/lib/pkgconfig/libiconv.pc
prefix={$libiconv_prefix}/
exec_prefix=\${prefix}/
libdir=\${prefix}/lib
includedir=\${prefix}/include/

Name: libiconv
Description: libiconv
Version: 1.17.0
Requires: zlib
Libs: -L\${libdir} -liconv
Libs.private: -lz
Cflags: -I\${includedir}

__example_PKGCONFIG_EOF__


EOF
            )
            ->withBinPath($libiconv_prefix . '/bin/')
            ->withLdflags('-L' . $libiconv_prefix . '/lib')
            ->withPkgConfig( $libiconv_prefix . '/lib/pkgconfig/')
            ->withPkgName('libiconv')
    );

    $p->withVariable('CPPFLAGS', '$CPPFLAGS -I' . $libiconv_prefix . '/include');
    $p->withVariable('LDFLAGS', '$LDFLAGS -L' . $libiconv_prefix . '/lib');
    $p->withVariable('LIBS', '$LIBS -liconv');
};

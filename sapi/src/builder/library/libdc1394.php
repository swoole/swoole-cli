<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $libdc1394_prefix = LIBDC1394_PREFIX;
    $openssl_prefix = OPENSSL_PREFIX;


    # 基于 IEEE 1394 的相机的开发人员提供完整的高级应用程序编程接口 (API)
    # libdc1394 提供完整的 API，包括摄像头检测（热插拔即将推出）、广播命令、总线资源管理（基本）、全功能控制（包括绝对值）、内存通道、外部和软件触发、支持所有视频模式（包括 16 位）模式和 800Mb/s 的 1394B 模式），使用 DMA 和完整 Format_7 控制进行视频捕获。

    //文件名称 和 库名称一致
    $lib = new Library('libdc1394');
    $lib->withHomePage('https://damien.douxchamps.net/ieee1394/libdc1394/')
        ->withLicense('https://tldp.org/HOWTO/html_single/libdc1394-HOWTO/#license', Library::LICENSE_LGPL)
        ->withManual('https://github.com/opencv/opencv.git')
        ->withFile('libdc1394-latest.tar.gz')
        ->withDownloadScript(
            'libdc1394',
            <<<EOF
               git clone https://git.code.sf.net/p/libdc1394/code libdc1394
EOF
        )
        ->withPrefix($libdc1394_prefix)
        ->withConfigure(
            <<<EOF


        # libtoolize -ci
        # autoreconf -fi


        case `uname` in
          Darwin*)
            glibtoolize --force --copy
            ;;
          *)
            libtoolize --force --copy
            ;;
        esac

        aclocal -I ./m4
        autoheader
        automake --foreign --add-missing --copy
        autoconf

        ./configure --help
        ./configure \
        --prefix={$libdc1394_prefix} \
        --enable-shared=no \
        --enable-static=yes \
        --without-x \
        --disable-examples \
        --disable-doxygen-doc

EOF
        )
        //->withPkgName('example')
        ->withBinPath($libdc1394_prefix . '/bin/')
        ->withDependentLibraries('libusb')
    ;

    $p->addLibrary($lib);
};

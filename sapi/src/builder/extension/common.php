<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = [
        'bzip2',
        'curl',
        'zlib',
        'iperf3',
        'libfido2',
        'opencl'
    ];
    $depends = [
        'openssl',
        'libconfig',
        'libnice'
    ];
    $depends = [
        'libg722',
        //'freetdm',
        'libpri',
    ];
    $depends = [
        'apr',
        'apr_util',
        'libexpat'
    ];
    $depends = [

        //'icecream',
        //'icecream_sundae',
        // 'mesa3d'
        // 'vulkan',
        // 'shaderc'
        // 'spirv_tools'
        // 'fdk_aac'
        // 'libfribidi'

        // 'libmongocrypt',
        // 'libmongoc',
        //'mongo_c_driver',
        //'dpdk',

        // 'libx265',
        //'boost',
        //'librime'
        'glog',
        'leveldb',
        'gflags',
        'libyaml_cpp',
        //'boost',
        'librime'
    ];
    $depends = ['libzookeeper'] ;
    $depends = ['libsctp'] ;
    $depends = ['libusrsctp'] ;
    $depends = ['bcg729'] ;
    $depends = ['util_linux'] ;
    $depends = ['elfutils'] ;
    $depends = ['snappy'] ;

    $depends = ['libdeflate'] ;
    $depends = ['libsharpyuv'] ;
    $depends = ['libyuv'] ;


    $depends = ['libunistring'] ;
    $depends = ['gettext'] ;
    # $depends = ['coreutils'] ;
    # $depends = ['gnulib'] ;
    $depends = ['libidn2'] ;

    $depends = ['libnl'] ;
    $depends = ['libmlx5'] ;
    $depends = ['libks'] ;
    $depends = ['confd'] ;
    $depends = ['libfvad'] ;
    $depends = ['dav1d'] ;

    $depends = ['glib'] ;
    $depends = ['pgsql'] ;
    $depends = ['libbsd'] ;
    $depends = ['util_linux'] ;
    $depends = ['pgsql_latest'] ;
    $depends = ['webrtc'] ;
    $depends = ['libbpf'] ;

    $depends = ['libelf'] ;

    $depends = ['riscv_gnu_toolchain'] ;
    $depends = ['fftw3'] ;
    $depends = ['lapack'] ;
    $depends = ['openblas'] ;
    $depends = ['openexr'] ;

    $depends = ['imath'] ;
    $depends = ['libdeflate'] ;
    $depends = ['openexr'] ;

    $depends = ['openjpeg'] ;
    $depends = ['harfbuzz'] ;
    $depends = ['libeigen'] ;
    $depends = ['libosmesa'] ;

    $depends = ['libsnmp'] ;
    $depends = ['opensips'] ;

    $depends = ['openldap'] ;
    $depends = ['libarchive'] ;
    $depends = ['pgsql_latest'] ;


    //$depends = ['libmongocrypt'] ;
    $depends = ['libpam'] ;
    $depends = ['libbson'] ;
    $depends = ['libmongoc'] ;



    $depends = ['oAuth'] ;
    $depends = ['coturn'] ;


    $depends = ['openssl'] ;


    $depends = ['libarchive'] ;
    $depends = ['libelf'] ;
    $depends = ['libxdp'] ;
    $depends = ['libbpf'] ;
    $depends = ['dpdk'] ;

    $depends = ['libmnl'] ;



    $depends = ['abseil_cpp'] ;
    $depends = ['protobuf'] ;
    $depends = ['protobuf_c'] ;
    $depends = ['unbound'] ;
    $depends = ['gnutls'] ;

    $depends = ['opencl'] ;
    $depends = ['vtk'] ;
    $depends = ['libelf'] ;
    $depends = ['libx265'] ;
    $depends = ['vlc'] ;
    $depends = ['libdc1394'] ;
    $depends = ['blas'] ;

    $depends = ['mpfr'] ;


    $depends = ['suitesparse'] ;
    $depends = ['libeigen'] ;

    $ext = (new Extension('common'))
        ->withHomePage('https://www.jingjingxyk.com')
        ->withManual('https://developer.baidu.com/article/detail.html?id=293377')
        ->withLicense('https://www.jingjingxyk.com/LICENSE', Extension::LICENSE_SPEC);
    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);
    $p->setExtHook('common', function (Preprocessor $p) {

        $workdir = $p->getWorkDir();
        $builddir = $p->getBuildDir();

        $cmd = <<<EOF
                mkdir -p {$workdir}/bin/
                cd {$builddir}/aria2/src
                cp -f aria2c {$workdir}/bin/

EOF;
        if ($p->getOsType() == 'macos') {
            $cmd .= <<<EOF
            otool -L {$workdir}/bin/aria2c
EOF;
        } else {
            $cmd .= <<<EOF
              file {$workdir}/bin/aria2c
              readelf -h {$workdir}/bin/aria2c
EOF;
        }
        return '';
        return $cmd;
    });
};

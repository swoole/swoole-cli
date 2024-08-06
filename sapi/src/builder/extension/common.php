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

    $depends = ['pgsql_latest'] ;


    $depends = ['oAuth'] ;
    $depends = ['coturn'] ;


    $depends = ['openssl'] ;



    $depends = ['libmnl'] ;



    $depends = ['abseil_cpp'] ;
    $depends = ['protobuf'] ;
    $depends = ['protobuf_c'] ;
    $depends = ['unbound'] ;
    $depends = ['gnutls'] ;

    $depends = ['opencl'] ;
    $depends = ['vtk'] ;
    $depends = ['libx265'] ;
    $depends = ['vlc'] ;
    $depends = ['libdc1394'] ;
    $depends = ['blas'] ;

    $depends = ['mpfr'] ;


    $depends = ['suitesparse'] ;
    $depends = ['libeigen'] ;
    $depends = ['libva'] ;
    $depends = ['libelf'] ;
    $depends = ['libbpf'] ;

    $depends = ['libarchive'] ;


    $depends = ['libbpf'] ;
    $depends = ['dpdk'] ;
    $depends = ['libxdp'] ;
    $depends = ['paho_mqtt'] ;
    $depends = ['janus_gateway'] ;
    $depends = ['libmysqlclient'] ;
    # $depends = ['mysql_connector'] ;
    //$depends = ['libmongocrypt'] ;
    $depends = ['libpam'] ;

    $depends = ['libmongoc'] ;
    $depends = ['libbson'] ;
    $depends = ['libwebp'] ;
    $depends = ['opencl'] ;

    $depends = ['svt_av1'] ;

    $depends = ['vulkan'] ;
    $depends = ['mesa3d'] ;
    $depends = ['ffmpeg'] ;
    $depends = ['dahdi_linux'] ;
    $depends = ['dahdi_tools'] ;
    $depends = ['libpri'] ;
    $depends = ['asterisk'] ;

    $depends = ['libde265'] ;
    $depends = ['libheif'] ;
    $depends = ['webrtc'] ;
    $depends = ['strongswan'] ;
    $depends = ['musl_cross_make'] ;
    $depends = ['libuuid'] ;
    $depends = ['python3'] ;
    $depends = ['libarchive'] ;
    $depends = ['gpac'] ;
    $depends = ['libx264'] ;
    $depends = ['libmongoc'] ;
    $depends = ['ovn'] ;
    $depends = ['liburing'] ;
    $depends = ['sdl2'] ;
    $depends = ['sndio_audio'] ;
    $depends = ['pulse_audio'] ;
    $depends = ['opensound_audio'] ;
    $depends = ['dav1d'] ;
    $depends = ['sdl2'] ;
    $depends = ['v4l_utils'] ;
    $depends = ['prometheus_client_c'] ;
    $depends = ['liboauth2'] ;
    $depends = ['depot_tools','libyuv'] ;
    $depends = ['libmongoc'] ;


    $ext = (new Extension('common'))
        ->withHomePage('https://www.jingjingxyk.com')
        ->withManual('https://developer.baidu.com/article/detail.html?id=293377')
        ->withLicense('https://www.jingjingxyk.com/LICENSE', Extension::LICENSE_SPEC);
    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);
    $p->withReleaseArchive('common', function (Preprocessor $p) {

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

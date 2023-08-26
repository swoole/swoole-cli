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
        //'libbson',
       // 'libmongocrypt',
       // 'libmongoc',
       //'mongo_c_driver',
       //'dpdk',
       //'libarchive'
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
    $depends = ['elfutils'] ;
    $depends = ['util_linux'] ;
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

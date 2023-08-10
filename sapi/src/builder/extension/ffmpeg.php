<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = [
        'ffmpeg'
    ];
    $ext = (new Extension('ffmpeg'))
        ->withHomePage('https://ffmpeg.org/')
        ->withLicense(
            'https://git.ffmpeg.org/gitweb/ffmpeg.git/blob/refs/heads/master:/LICENSE.md',
            Extension::LICENSE_LGPL
        )->withManual('https://ffmpeg.org/documentation.html')
    ;

    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);
    $p->setExtHook('ffmpeg', function (Preprocessor $p) {

        $workdir = $p->getWorkDir();
        $builddir = $p->getBuildDir();
        $ffmpeg_prefix = FFMPEG_PREFIX;

        $cmd = <<<EOF
                mkdir -p {$workdir}/bin/ffmpeg/
                cd {$ffmpeg_prefix}/
                cp -rf bin {$workdir}/bin/ffmpeg/

EOF;
        if ($p->getOsType() == 'macos') {
            $cmd .= <<<EOF
            otool -L {$workdir}/bin/ffmpeg/bin/ffmpeg
            {$workdir}/bin/ffmpeg/bin/ffmpeg -h
EOF;
        } else {
            $cmd .= <<<EOF
              file {$workdir}/bin/ffmpeg/bin/ffmpeg
              readelf -h {$workdir}/bin/ffmpeg/bin/ffmpeg

EOF;
        }
        return $cmd;
    });
};

<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = [
        'opencv_docs'
    ];
    $ext = (new Extension('opencv_docs'))
        ->withHomePage('https://opencv.org/')
        ->withManual('https://docs.opencv.org')
        ->withLicense('https://opencv.org/license/', Extension::LICENSE_APACHE2);
    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);

    $p->withReleaseArchive('opencv_docs', function (Preprocessor $p) {

        $workdir = $p->getWorkDir();
        $builddir = $p->getBuildDir();

        $cmd = <<<EOF
                mkdir -p {$workdir}/bin/
                test -d {$workdir}/bin/opencv_docs && rm -rf {$workdir}/bin/opencv_docs
                test -d {$workdir}/bin/opencv-docs && rm -rf {$workdir}/bin/opencv-docs

                mkdir -p {$workdir}/bin/opencv-docs

                cd {$builddir}/opencv_docs/
                ls -lh build/doc/doxygen/
                cp -rf build/doc/doxygen/html/*  {$workdir}/bin/opencv-docs/

                cd {$workdir}/bin/

                tar -czvf {$workdir}/opencv-docs-vlatest.tar.gz opencv-docs

                cd {$workdir}/

EOF;
        return $cmd;
    });
};

<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = [
        'ffmpeg',
        'opencv'
    ];
    $ext = (new Extension('opencv'))
        ->withHomePage('https://opencv.org/')
        ->withManual('https://docs.opencv.org')
        ->withLicense('https://opencv.org/license/', Extension::LICENSE_APACHE2);
    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);
};

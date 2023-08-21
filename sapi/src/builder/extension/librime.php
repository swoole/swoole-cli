<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = [
        'librime'
    ];
    $ext = (new Extension('librime'))
        ->withHomePage('https://rime.im/')
        ->withManual('https://github.com/rime')
        ->withLicense('https://github.com/rime/librime/blob/master/LICENSE', Extension::LICENSE_BSD);
    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);
};

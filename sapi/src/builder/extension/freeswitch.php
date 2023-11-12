<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = [
       // 'freeswitch',
        'freeswitch_release'
    ];
    $ext = (new Extension('freeswitch'))
        ->withHomePage('http://www.freeswitch.org')
        ->withManual('http://www.freeswitch.org.cn/')
        ->withLicense('https://github.com/signalwire/freeswitch/blob/master/LICENSE', Extension::LICENSE_SPEC);
    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);
};

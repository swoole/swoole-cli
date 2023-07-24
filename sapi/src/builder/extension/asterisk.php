<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = [
        'asterisk'
    ];
    $ext = (new Extension('asterisk'))
        ->withHomePage('https://www.jingjingxyk.com')
        ->withManual('https://developer.baidu.com/article/detail.html?id=293377')
        ->withLicense('https://www.jingjingxyk.com/LICENSE', Extension::LICENSE_SPEC);
    call_user_func_array([$ext, 'withDependentLibraries'], $depends);
    $p->addExtension($ext);

};

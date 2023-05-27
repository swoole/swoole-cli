<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = [
        'openssl',
        'socat'
    ];
    $ext = (new Extension('socat'))
        ->withOptions('')
        ->withHomePage('https://www.jingjingxyk.com')
        ->withManual('https://developer.baidu.com/article/detail.html?id=293377') //如何选开源许可证？
        ->withLicense('https://www.jingjingxyk.com/LICENSE', Extension::LICENSE_GPL);
    call_user_func_array([$ext, 'depends'], $depends);
    $p->addExtension($ext);
};

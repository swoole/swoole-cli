<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = [
        'privoxy'
    ];
    $ext = (new Extension('privoxy'))
        ->withHomePage('https://www.privoxy.org')
        ->withManual('https://www.privoxy.org/user-manual/quickstart.html')
        ->withLicense('https://www.privoxy.org/gitweb/?p=privoxy.git;a=blob_plain;f=LICENSE.GPLv3;h=f288702d2fa16d3cdf0035b15a9fcbc552cd88e7;hb=HEAD', Extension::LICENSE_GPL);
    call_user_func_array([$ext, 'depends'], $depends);
    $p->addExtension($ext);
};

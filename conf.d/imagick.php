<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension((new Extension('imagick'))
        ->withOptions('--with-imagick=/usr/imagemagick')
        ->withPeclVersion('3.6.0')
        ->withHomePage('https://github.com/Imagick/imagick')
        ->withLicense('https://github.com/Imagick/imagick/blob/master/LICENSE', Extension::LICENSE_PHP)
    );
};

<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension((new Extension('yaml'))
        ->withOptions('--with-yaml=/usr/libyaml')
        ->withPeclVersion('2.2.2')
        ->withHomePage('https://github.com/php/pecl-file_formats-yaml')
        ->withLicense('https://github.com/php/pecl-file_formats-yaml/blob/php7/LICENSE', Extension::LICENSE_MIT)
    );
};

<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('yaml'))
            ->withOptions('--with-yaml=' . LIBYAML_PREFIX)
            ->withPeclVersion('2.2.2')
            ->withHomePage('https://github.com/php/pecl-file_formats-yaml')
            ->withLicense('https://github.com/php/pecl-file_formats-yaml/blob/php7/LICENSE', Extension::LICENSE_MIT)
            ->withDependentLibraries('libyaml')
    );
};

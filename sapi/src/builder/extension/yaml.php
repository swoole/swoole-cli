<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('yaml'))
            ->withOptions('--with-yaml=' . LIBYAML_PREFIX)
            ->withPeclVersion('2.3.0')
            ->withFileHash('sha256', 'bc8404807a3a4dc896b310af21a7f8063aa238424ff77f27eb6ffa88b5874b8a')
            ->withHomePage('https://github.com/php/pecl-file_formats-yaml')
            ->withLicense('https://github.com/php/pecl-file_formats-yaml/blob/php7/LICENSE', Extension::LICENSE_MIT)
            ->withDependentLibraries('libyaml')
    );
};

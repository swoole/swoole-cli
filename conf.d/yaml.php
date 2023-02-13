<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addLibrary(
        (new Library('libyaml', '/usr/libyaml'))
            ->withUrl('https://pyyaml.org/download/libyaml/yaml-0.2.5.tar.gz')
            ->withConfigure('./configure --prefix=/usr/libyaml --enable-static --disable-shared')
            ->withPkgName('yaml-0.1')
            ->withLicense('https://pyyaml.org/wiki/LibYAML', Library::LICENSE_MIT)
            ->withHomePage('https://pyyaml.org/wiki/LibYAML')
    );
    $p->addExtension((new Extension('yaml'))
        ->withOptions('--with-yaml=/usr/libyaml')
        ->withPeclVersion('2.2.2')
        ->withHomePage('https://github.com/php/pecl-file_formats-yaml')
        ->withLicense('https://github.com/php/pecl-file_formats-yaml/blob/php7/LICENSE', Extension::LICENSE_MIT)
        ->depends('libyaml')
    );
};

<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $p->addLibrary(
        (new Library('libyaml'))
            ->withHomePage('https://pyyaml.org/wiki/LibYAML')
            ->withManual('https://pyyaml.org/wiki/LibYAML')
            ->withLicense('https://pyyaml.org/wiki/LibYAML', Library::LICENSE_MIT)
            ->withUrl('https://pyyaml.org/download/libyaml/yaml-0.2.5.tar.gz')
            ->withPrefix(LIBYAML_PREFIX)
            ->withConfigure('./configure --prefix=' . LIBYAML_PREFIX . ' --enable-static --disable-shared')
            ->withPkgName('yaml-0.1')

    );
};

<?php


use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $depends = ['musl_cross_make'];
    $options = ' ';
    $ext = (new Extension('musl_cross_make'))
        ->withOptions($options)
        ->withHomePage('https://github.com/richfelker/musl-cross-make.git')
        ->withLicense('https://github.com/richfelker/musl-cross-make?tab=MIT-1-ov-file#readme', Library::LICENSE_MIT)
        ->withManual('https://github.com/richfelker/musl-cross-make/blob/master/README.md');
    $ext->withDependentLibraries(...$depends);
    $p->addExtension($ext);
};

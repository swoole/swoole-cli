<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('imagick'))
            ->withOptions('--with-imagick=' . IMAGEMAGICK_PREFIX)
            ->withPeclVersion('3.6.0')
            ->withFileHash('md5', 'f7b5e9b23fb844e5eb035203d316bc63')
            ->withHomePage('https://github.com/Imagick/imagick')
            ->withLicense('https://github.com/Imagick/imagick/blob/master/LICENSE', Extension::LICENSE_PHP)
            ->withMd5sum('f7b5e9b23fb844e5eb035203d316bc63')
            ->withDependentLibraries('imagemagick')
            ->withDependentExtensions('tokenizer')
    );
};
//构建的过程中，会自动 下载 PHP-Parser
//https://codeload.github.com/nikic/PHP-Parser/

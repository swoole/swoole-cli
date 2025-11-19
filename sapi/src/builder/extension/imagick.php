<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('imagick'))
            ->withOptions('--with-imagick=' . IMAGEMAGICK_PREFIX)
            ->withPeclVersion('3.8.0')
            ->withFileHash('md5', 'e6185b1412e65a91e598d1c79a00aeb9')
            ->withHomePage('https://github.com/Imagick/imagick')
            ->withLicense('https://github.com/Imagick/imagick/blob/master/LICENSE', Extension::LICENSE_PHP)
            ->withDependentLibraries('imagemagick')
            ->withDependentExtensions('tokenizer')
            ->withBuildCached(false)
    );
};


# 构建 imagick 扩展时 会自动下载 https://github.com/nikic/PHP-Parser  源码


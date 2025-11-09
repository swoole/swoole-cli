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

    // 扩展钩子
    $p->withBeforeConfigureScript('imagick', function (Preprocessor $p) {
        $workDir = $p->getPhpSrcDir();
        $cmd = <<<EOF
        cd {$workDir}
        sed -i.backup "s/php_strtolower(/zend_str_tolower(/" ext/imagick/imagick.c
EOF;

        if (BUILD_CUSTOM_PHP_VERSION_ID >= 8040) {
            //参考
            //https://github.com/swoole/swoole-src/blob/4787a8a0e8b4adb0e8643901d2b5bae4fafe0876/ext-src/swoole_redis_server.cc#L162
            $cmd .= PHP_EOL;
        } else {
            $cmd = '';
        }
        return $cmd;
    });
};


# 构建 imagick 扩展时 会自动下载 https://github.com/nikic/PHP-Parser  源码


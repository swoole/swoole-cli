<?php


use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    return null;//待改进
    //PHP 构建选项
    $options = ' --enable-opentelemetry ';

    $ext = (new Extension('opentelemetry'))
        ->withOptions($options)
        ->withHomePage('https://opentelemetry.io/')
        ->withManual('https://github.com/open-telemetry/opentelemetry-php-instrumentation.git')
        ->withLicense('https://github.com/open-telemetry/opentelemetry-php-instrumentation/blob/main/LICENSE', Extension::LICENSE_APACHE2)
        ->withFile('opentelemetry-latest.tar.gz')
        ->withDownloadScript(
            'ext', # 待打包目录名称
            <<<EOF
            git clone -b main --depth=1 https://github.com/open-telemetry/opentelemetry-php-instrumentation.git

            mv opentelemetry-php-instrumentation/ext  ext

EOF
        );
    $p->addExtension($ext);
};

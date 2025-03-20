<?php


use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
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
    $p->withBeforeConfigureScript('opentelemetry', function (Preprocessor $p) {
        $workDir = $p->getPhpSrcDir();
        $cmd=<<<EOF
        cd {$workDir}/ext/opentelemetry/

         sed -i '' 's/static void check_conflicts()/static void check_conflicts(void)/' opentelemetry.c
         sed -i '' 's/static otel_observer \*create_observer()/static otel_observer \*create_observer(void)/' otel_observer.c
EOF;
        return $cmd;
    });
    $p->addExtension($ext);
};

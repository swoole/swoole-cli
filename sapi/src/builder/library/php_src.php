<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $php_install_prefix = BUILD_PHP_INSTALL_PREFIX;
    $php_src = $p->getPhpSrcDir();
    $build_dir = $p->getBuildDir();

    $build_type = $p->getInputOption('with-build-type');
    $cmd = '';
    if ($build_type == 'dev') {
        $cmd = <<<EOF

            TMP_EXT_DIR=/{$build_dir}/php-tmp-ext-dir/

            test -d \$TMP_EXT_DIR && rm -rf \$TMP_EXT_DIR

            mkdir -p \$TMP_EXT_DIR

            cd ext

            cp -rf date \$TMP_EXT_DIR
            cp -rf hash \$TMP_EXT_DIR
            cp -rf json \$TMP_EXT_DIR
            cp -rf pcre \$TMP_EXT_DIR
            test -d random && cp -rf random \$TMP_EXT_DIR
            cp -rf reflection \$TMP_EXT_DIR
            cp -rf session \$TMP_EXT_DIR
            cp -rf spl \$TMP_EXT_DIR
            cp -rf standard \$TMP_EXT_DIR
            cp -rf date \$TMP_EXT_DIR
            cp -rf phar \$TMP_EXT_DIR

            cd ..

            rm -rf ext
            mv \$TMP_EXT_DIR ext
EOF;
    }


    $p->addLibrary(
        (new Library('php_src'))
            ->withUrl('https://github.com/php/php-src/archive/refs/tags/php-' . BUILD_PHP_VERSION . '.tar.gz')
            ->withHomePage('https://www.php.net/')
            ->withLicense('https://github.com/php/php-src/blob/master/LICENSE', Library::LICENSE_PHP)
            ->withPrefix($php_install_prefix)
            ->withCleanBuildDirectory()
            ->withBuildScript(
                <<<EOF
            if test -d {$php_src} ; then
                rm -rf {$php_src}
            fi

            {$cmd}

            cd ..
            cp -rf php_src {$php_src}
            cd {$build_dir}/php_src

EOF
            )
    );
};

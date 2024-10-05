<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $php_install_prefix = BUILD_PHP_INSTALL_PREFIX;
    $php_tag = BUILD_PHP_VERSION;
    $php_src = $p->getPhpSrcDir();
    $build_dir = $p->getBuildDir();

    $p->addLibrary(
        (new Library('php'))
            ->withUrl('https://github.com/php/php-src/archive/refs/tags/php-' . $php_tag . '.tar.gz')
            ->withHomePage('https://www.php.net/')
            ->withLicense('https://github.com/php/php-src/blob/master/LICENSE', Library::LICENSE_PHP)
            ->withPrefix($php_install_prefix)
            ->withBuildCached(false)
            ->withInstallCached(false)
            ->withBuildScript(
                <<<EOF
            cd {$build_dir}/
            test -d {$php_src} && rm -rf {$php_src}

            cp -rf php {$php_src}
            rm -rf php
            cd {$build_dir}/
EOF
            )
            ->withLdflags('')
            ->withPkgConfig('')
    );
};

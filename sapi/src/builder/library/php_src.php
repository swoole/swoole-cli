<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

return function (Preprocessor $p) {
    $php_install_prefix = BUILD_PHP_INSTALL_PREFIX;
    $php_src = $p->getPhpSrcDir();
    $build_dir = $p->getBuildDir();

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
            cd ..
            cp -rf php_src {$php_src}
            cd {$build_dir}/php_src

EOF
            )
            ->withLdflags('')
            ->withPkgConfig('')
            ->withBuildLibraryCached(false)
    );
};

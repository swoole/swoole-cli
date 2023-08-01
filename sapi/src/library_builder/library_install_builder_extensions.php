<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;

function install_php_parser($p)
{
    $workDir = $p->getWorkDir();
    $p->addLibrary(
        (new Library('php_extension_php_parser'))
            ->withHomePage('https://github.com/nikic/PHP-Parser.git')
            ->withLicense('https://github.com/nikic/PHP-Parser/blob/4.x/LICENSE', Library::LICENSE_BSD)
            ->withUrl('https://github.com/nikic/PHP-Parser/archive/refs/tags/v4.15.3.tar.gz')
            ->withFile('php-8.1.12.tar.gz')
            ->withManual('https://www.php.net/docs.php')
            ->withLabel('php_internal_extension')
            ->withCleanBuildDirectory()
            ->withConfigure('return 0')
            ->withSkipDownload()
            ->disablePkgName()
            ->disableDefaultPkgConfig()
            ->disableDefaultLdflags()
            ->withSkipBuildLicense()
            ->withSkipBuildInstall()
    );
}

function install_php_internal_extensions($p)
{

}

function install_php_extension_micro(Preprocessor $p)
{
    $p->addLibrary(
        (new Library('php_extension_micro'))
            ->withHomePage('https://github.com/dixyes/phpmicro')
            ->withUrl('https://github.com/dixyes/phpmicro/archive/refs/heads/master.zip')
            ->withFile('latest-phpmicro.zip')
            ->withLicense('https://github.com/dixyes/phpmicro/blob/master/LICENSE', Library::LICENSE_APACHE2)
            ->withManual('https://github.com/dixyes/phpmicro#readme')
            ->withLabel('php_extension')
            ->withCleanBuildDirectory()
            ->withUntarArchiveCommand('unzip')
            ->disableDefaultPkgConfig()
            ->disableDefaultLdflags()
            ->disablePkgName()
            ->withSkipBuildInstall()
    );
}


function install_php_extension_swow(Preprocessor $p)
{

}

function install_php_extension_wasm(Preprocessor $p)
{
    $workDir = $p->getWorkDir();
    $buildDir = $p->getBuildDir();
    $p->addLibrary(
        (new Library('php_extension_wasm'))
            ->withHomePage('https://github.com/wasmerio/wasmer-php.git')
            ->withUrl('https://github.com/wasmerio/wasmer-php/archive/refs/tags/1.1.0.tar.gz')
            ->withFile('wasmer-php-1.1.0.tar.gz')
            ->withLicense('https://github.com/wasmerio/wasmer-php/blob/master/LICENSE', Library::LICENSE_MIT)
            ->withManual('https://github.com/wasmerio/wasmer-php.git')
            ->withLabel('php_extension')
            ->withCleanBuildDirectory()
            ->withBuildScript(
                "
              ls -lh ./ext
              pwd
              cp -rf ext  {$workDir}/ext/wasm
            "
            )
            ->disableDefaultPkgConfig()
            ->disableDefaultLdflags()
            ->disablePkgName()
        //->withSkipBuildInstall()
    );
}

function install_php_extension_zookeeper(Preprocessor $p)
{

}

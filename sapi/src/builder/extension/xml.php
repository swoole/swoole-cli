<?php

use SwooleCli\Library;
use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    /*
    $p->addExtension(
        (new Extension('xml'))
            ->withHomePage('https://www.php.net/xml')
            ->withOptions(
                '--enable-xml --enable-simplexml --enable-xmlreader --enable-xmlwriter --enable-dom --with-libxml'
            )
            ->withDependentLibraries('libxml2')
    );
    */

    $options = ' --with-libxml ';
    # $options .= ' --with-libxml-dir=' . LIBXML2_PREFIX;
    $p->addExtension(
        (new Extension('libxml'))
            ->withHomePage('https://www.php.net/libxml')
            ->withOptions($options)
            ->withDependentLibraries('libxml2')
    );
    $options = ' --enable-xml ';

    $p->addExtension(
        (new Extension('xml'))
            ->withHomePage('https://www.php.net/xml')
            ->withOptions($options)
    );

    $options = ' --enable-xmlreader';
    $p->addExtension(
        (new Extension('xmlreader'))
            ->withHomePage('https://www.php.net/xmlreader')
            ->withOptions($options)
    );
    $options = ' --enable-xmlwriter';
    $p->addExtension(
        (new Extension('xmlwriter'))
            ->withHomePage('https://www.php.net/xmlwriter')
            ->withOptions($options)
    );
    $options = ' --enable-dom';
    $p->addExtension(
        (new Extension('dom'))
            ->withHomePage('https://www.php.net/dom')
            ->withOptions($options)
    );
    $options = ' --enable-simplexml';
    $p->addExtension(
        (new Extension('simplexml'))
            ->withHomePage('https://www.php.net/simplexml')
            ->withOptions($options)
    );
};

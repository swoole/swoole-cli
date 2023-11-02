<?php


use SwooleCli\Preprocessor;
use SwooleCli\Library;

return function (Preprocessor $p) {
    $lib = new Library('mp3lame');
    $lib->withHomePage('https://lame.sourceforge.io/')
        ->withLicense('https://lame.sourceforge.io/links.php', Library::LICENSE_SPEC)
        ->withUrl('https://sourceforge.net/projects/lame/files/lame/3.100/lame-3.100.tar.gz');
    $p->addLibrary($lib);
};

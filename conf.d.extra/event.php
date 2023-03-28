<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $libevent_prefix = LIBEVENT_PREFIX;
    $options = '--with-event-core';
    $options .= ' --with-event-extra';
    $options .= ' --with-event-openssl';
    $options .= ' --enable-event-sockets';
    $options .= ' --with-event-libevent-dir=' . $libevent_prefix;
    $p->addExtension(
        (new Extension('event'))
            ->withOptions($options)
            ->withHomePage('https://bitbucket.org/osmanov/pecl-event')
            ->withLicense('https://bitbucket.org/osmanov/pecl-event/src/master/LICENSE', Extension::LICENSE_PHP)
            ->withManual('http://docs.php.net/event')
            ->withPeclVersion('3.0.8')
    );
};

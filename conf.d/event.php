<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $libevent_prefix = LIBEVENT_PREFIX;
    $p->addExtension(
        (new Extension('event'))
            ->withOptions('--with-event-core ---event-pthreads--with-event-extra --with-event-openssl --enable-event-sockets --with-event-libevent-dir='.$libevent_prefix)
            ->withHomePage('https://bitbucket.org/osmanov/pecl-event')
            ->withLicense('https://bitbucket.org/osmanov/pecl-event/src/master/LICENSE', Extension::LICENSE_PHP)
            ->withManual('http://docs.php.net/event')
            ->withPeclVersion('3.0.8')
    );

};

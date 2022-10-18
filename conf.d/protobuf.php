<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension((new Extension('protobuf'))
        ->withOptions('--enable-protobuf')
        ->withPeclVersion('3.21.6')
        ->withHomePage('https://developers.google.com/protocol-buffers'));

    $p->setExtCallback('protobuf', function (Preprocessor $p) {
        // compatible with redis
        if ($p->osType === 'macos') {
            echo `sed -i '.bak' 's/arginfo_void,/arginfo_void_protobuf,/g' ext/protobuf/*.c ext/protobuf/*.h ext/protobuf/*.inc`;
            echo `find ext/protobuf/ -name \*.bak | xargs rm -f`;
        } else {
            echo `sed -i 's/arginfo_void,/arginfo_void_protobuf,/g' ext/protobuf/*.c ext/protobuf/*.h ext/protobuf/*.inc`;
        }
    });
};

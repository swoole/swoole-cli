<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('protobuf'))
            ->withOptions('--enable-protobuf')
            ->withPeclVersion('3.21.6')
            ->withPeclVersion('3.23.2')
            ->withHomePage('https://developers.google.com/protocol-buffers')
            ->withManual('https://protobuf.dev/reference/php/php-generated/')
            ->withDependentExtensions('sockets')
    );

    $p->setExtHook('protobuf', function (Preprocessor $p) {
        // compatible with redis
        $workdir= $p->getphpSrcDir();
        if ($p->getOsType() === 'macos') {
            $cmd = <<<EOF
                cd {$workdir}
                sed -i '.bak' 's/arginfo_void,/arginfo_void_protobuf,/g' ext/protobuf/*.c ext/protobuf/*.h ext/protobuf/*.inc
                find ext/protobuf/ -name \*.bak | xargs rm -f
EOF;
        } else {
            $cmd = <<<EOF
                cd {$workdir}
                sed -i 's/arginfo_void,/arginfo_void_protobuf,/g' ext/protobuf/*.c ext/protobuf/*.h ext/protobuf/*.inc
EOF;
        }
        return '';
        return $cmd;
    });
};

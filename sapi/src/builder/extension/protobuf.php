<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('protobuf'))
            ->withOptions('--enable-protobuf')
            ->withPeclVersion('4.29.2')
            ->withFileHash('md5', 'bf1b1d37bf5dd16899e317320a269770')
            ->withHomePage('https://github.com/protocolbuffers/protobuf')
            ->withManual('https://protobuf.dev/reference/php/php-generated/')
            ->withDependentExtensions('sockets')
    );

    $p->withBeforeConfigureScript('protobuf', function (Preprocessor $p) {
        // compatible with redis
        $workdir = $p->getWorkDir();
        if ($p->isMacos()) {
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
        return $cmd;
    });
};

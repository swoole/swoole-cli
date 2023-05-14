<?php

use SwooleCli\Preprocessor;
use SwooleCli\Extension;

return function (Preprocessor $p) {
    $p->addExtension(
        (new Extension('protobuf'))
            ->withOptions('--enable-protobuf')
            ->withPeclVersion('3.21.6')
            ->withHomePage('https://developers.google.com/protocol-buffers')
            ->withManual('https://protobuf.dev/reference/php/php-generated/')
    );

    $p->setExtCallback('protobuf', function (Preprocessor $p) {
        $work_dir=$p->getWorkDir();

        $cmd=" cd {$work_dir}/ext/xlswriter " . PHP_EOL;

        // compatible with redis
        if ($p->getOsType() === 'macos') {
            $cmd .="sed -i '.bak' 's/arginfo_void,/arginfo_void_protobuf,/g' ext/protobuf/*.c ext/protobuf/*.h ext/protobuf/*.inc " . PHP_EOL ;
            $cmd .="find ext/protobuf/ -name \*.bak | xargs rm -f " . PHP_EOL ;
        } else {
            $cmd .="sed -i 's/arginfo_void,/arginfo_void_protobuf,/g' ext/protobuf/*.c ext/protobuf/*.h ext/protobuf/*.inc " . PHP_EOL ;
        }

        $cmd .= $cmd . PHP_EOL . "cd {$work_dir}/" .PHP_EOL;

        return $cmd;
    });
};

<?php
return [
    'name' => 'swoole-cli',
    /* Notice: Sort by dependency */
    'files' => [
        'helper.php',
    ],
    'output' => __DIR__ . '/../cli/library.h',
    'stripComments' => true,
    'checkFileChange' => false,
];

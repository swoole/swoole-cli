<?php


$message = '';
# pool 目录为web目录
$web_document = realpath(__DIR__ . '/../../pool/');

$message .= <<<EOF
 document_root:
     {$web_document}
EOF;
$message .= PHP_EOL;


$http = new Swoole\Http\Server("0.0.0.0", 9503);


$http->set([
    'document_root' => $web_document,
    'enable_static_handler' => true,
    'http_autoindex' => true,
    'http_index_files' => ['indesx.html', 'index.txt'],
    'http_compression_types' => [
        'text/html',
        'application/json'
    ],
]);


$message .= <<<EOF

    download-box web server :  listen http://0.0.0.0:9503

EOF;
printf($message);

$http->on('request', function ($request, $response) {
    $response->end("<h1> swoole-cli download-box </h1>");
});
$http->start();

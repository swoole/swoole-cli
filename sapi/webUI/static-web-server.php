<?php

$http = new Swoole\Http\Server("0.0.0.0", 9501);
$message = <<<EOF

    dashboard  listen http://0.0.0.0:9501

EOF;
printf($message);


$http->set([
    'document_root' => __DIR__ . '/public/',
    'enable_static_handler' => true,
    'http_autoindex' => true,
    'http_index_files' => ['index.html', 'index.txt'],
]);
$http->on('request', function ($request, $response) {
    // $response->end("<h1>Hello Swoole. #" . rand(1000, 9999) . "</h1>");
    $response->redirect('/index.html', 301);
});
$http->start();

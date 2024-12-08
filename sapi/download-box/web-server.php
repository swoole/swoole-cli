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


# 如果使用了systemd或supervisor管理Swoole服务器程序，就不要开启daemonize

# reload 知识点
# https://wiki.swoole.com/#/server/methods?id=reload
# https://wiki.swoole.com/#/question/use?id=swoole如何正确的重启服务

# top -p `pidof dockerd`
# Linux 信号列表
# https://wiki.swoole.com/#/other/signal
# kill -SIGHUP $(pidof php)
# kill -SIGHUP $(ps aux | grep 'php sapi/download-box/web-server.php' | grep -v grep | awk '{print $2}')

# 关闭

# kill -SIGKILL $(ps aux | grep 'php sapi/download-box/web-server.php' | grep -v grep | awk '{print $2}')


$http->set([
    'document_root' => $web_document,
    'enable_static_handler' => true,
    'http_autoindex' => true,
    'http_index_files' => ['indesx.html', 'index.txt'],
    'http_compression_types' => [
        'text/html',
        'application/json'
    ],
    'display_errors' => true,
    'daemonize' => true,
    'log_file' => "/tmp/swoole-cli-download-box-web.log"
    # 'ssl_cert_file' => __DIR__.'/config/ssl.crt',
    # 'ssl_key_file' => __DIR__.'/config/ssl.key',
]);


$message .= <<<EOF

    download-box web server :  listen http://0.0.0.0:9503

EOF;
printf($message);

$http->on('request', function ($request, $response) {
    $response->end("<h1> swoole-cli download-box </h1>");
});
$http->start();

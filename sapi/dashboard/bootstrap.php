<?php

use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\CloseFrame;
use Swoole\Coroutine\Http\Server;
use Swoole\Coroutine\Channel;
use Swoole\Coroutine;

use function Swoole\Coroutine\run;

run(function () {
    $server = new Server('0.0.0.0', 9502, false);

    $message = <<<EOF

    dashboard  listen http://0.0.0.0:9502

EOF;
    printf($message);

    $server->handle('/', function ($request, $response) use ($server) {
        //https://developer.mozilla.org/zh-CN/docs/Web/HTTP/Basics_of_HTTP/MIME_types

        $request_uri = str_replace('..', '', $request->server['request_uri']);
        $request_uri = str_replace('//', '/', $request_uri);
        $path_info = explode('/', $request_uri);
        $file = __DIR__ . '/public/' . $request_uri;
        if (isset($path_info[1]) && isset($path_info[2])) {
            $prefix = '/' . $path_info[1] . '/';
        } else {
            $prefix = '/';
        }
        if ($prefix == '/js/') {
            $response->header('content-type', 'application/javascript');
        } elseif ($prefix == '/css/') {
            $response->header('content-type', 'text/css');
        } elseif ($prefix == '/data/') {
            $response->header('content-type', 'application/json;charset=utf-8');
        } elseif ($request->server['request_uri'] == '/' || $request->server['request_uri'] == '/index.html') {
            $response->header('content-type', 'text/html;charset=utf-8');
            $response->end(file_get_contents(__DIR__ . '/public/index.html'));
            return null;
        } else {
            $response->header('content-type', 'application/octet-stream');
        }
        if (is_file($file)) {
            $response->end(file_get_contents($file));
        } else {
            $response->status(404);
        }
    });
    $server->handle('/api', function (Request $request, Response $response) {
        var_dump($request->header);
        $response->header('Content-Type', 'application/json; charset=utf-8');
        $response->header('access-control-allow-credentials', 'true');

        $response->header('access-control-allow-methods', 'GET,HEAD,POST,OPTIONS');
        $response->header('access-control-allow-headers', 'content-type,Authorization');
        $origin = empty($request->header['origin']) ? '*' : $request->header['origin'];
        $response->header('access-control-allow-origin', $origin);
        $request_method = empty($request->header['request_method']) ? '' : $request->header['request_method'];
        if ($request_method == "OPTIONS") {
            $response->header('Access-Control-Allow-Private-Network', 'true');
            $response->status(200);
            $response->end();
            return null;
        }

        list($controller, $action) = explode('/', trim($request->server['request_uri'], '/'));

        $controller = empty($controller) ? 'api' : $controller;
        $controller = preg_match('/\w+/', $controller) ? $controller : 'api';
        $controller = ucfirst($controller) . 'Controller';

        $action = empty($action) ? 'index' : $action;
        $action = preg_match('/\w+/', $action) ? $action : 'index';
        $action = lcfirst($action) . 'Action';

        var_dump($action);
        $parameter = $request->getContent();
        $parameter = json_decode($parameter, true);
        var_dump($parameter);

        $word_dir = realpath(__DIR__ . '/../../');
        $runtime = realpath($word_dir . '/bin/runtime');
        if ($action === 'changeBranchAction') {
            $branch_name = $parameter['data']['branch_name'];
            $cmd = <<<EOF
         cd $word_dir
         git checkout $branch_name

EOF;
            ob_start();
            passthru($cmd, $result_code);
            $result = ob_get_contents();
            ob_end_clean();
            echo $result;
        } elseif ($action === 'branchListAction') {
            $cmd = <<<EOF
         cd $word_dir
         git branch

EOF;
            ob_start();
            passthru($cmd, $result_code);
            $result = ob_get_contents();
            ob_end_clean();

            $result = explode("\n", $result);
            array_walk($result, function ($value, $key) use (&$result) {
                $result[$key] = trim($value);
                if (empty($value)) {
                    unset($result[$key]);
                }
            });
            var_dump($result);
        } elseif ($action === 'extensionListAction') {
            $cmd = <<<EOF
            cd $word_dir/sapi/src/builder/extension
            ls .

EOF;
            ob_start();
            passthru($cmd, $result_code);
            $result = ob_get_contents();
            ob_end_clean();

            $result = explode("\n", $result);

            array_walk($result, function ($value, $key) use (&$result) {
                $result[$key] = str_replace('.php', '', trim($value));
                if (empty($value)) {
                    unset($result[$key]);
                }
            });
            var_dump($result);
        } elseif ($action === 'defaultExtensionListAction') {
            $result = [];


            $fp = new \SplFileObject($word_dir . '/sapi/src/Preprocessor.php', 'r');
            if ($fp) {
                $fp->seek(74);
                while (!$fp->eof()) {
                    $line = $fp->current();
                    $cursor = $fp->ftell();
                    $line_no = $fp->key();
                    //$line = $fp->fgets();
                    $fp->next();
                    $ext_name = trim(str_replace(['\'', '\n', ','], '', $line));
                    echo $ext_name . PHP_EOL;
                    if (!empty($ext_name)) {
                        $result[] = $ext_name;
                    }
                    if ($line_no >= 110) {
                        break;
                    }
                }
                $fp = null;
            }
        }

        $cmd = <<<EOF
        cd $word_dir
        export PATH=${runtime}:\$PATH
        php prepare.php --with-build-type=release +apcu +ds

EOF;


        try {
            $response->end(
                json_encode(
                    [
                        'code' => 200,
                        "msg" => 'success',
                        "data" => $result
                    ],
                    JSON_UNESCAPED_UNICODE
                )
            );
        } catch (\RuntimeException $e) {
            echo $e->getMessage();
            $response->end(json_encode(["code" => 500, 'msg' => 'system error' . $e->getMessage()]));
        }
    });

    $server->handle('/websocket', function (Request $request, Response $ws) use ($server) {
        $ws->upgrade();
        while (true) {
            $frame = $ws->recv();
            if ($frame === '') {
                $ws->close();
                break;
            } else {
                if ($frame === false) {
                    echo 'errorCode: ' . swoole_last_error() . "\n";
                    $ws->close();
                    break;
                } else {
                    if ($frame->data == 'close' || get_class($frame) === CloseFrame::class) {
                        $ws->close();
                        break;
                    }
                    $ws->push("Hello {$frame->data}!");
                    $ws->push("How are you, {$frame->data}?");
                }
            }
        }
    });

    $server->handle('/stop', function ($request, $response) use ($server) {
        $response->end("<h1>Stop</h1>");
        $server->fp->fclose();
        $server->shutdown();
    });

    $server->start();
});

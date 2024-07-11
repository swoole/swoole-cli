<?php

use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\CloseFrame;
use Swoole\Coroutine\Http\Server;
use Swoole\Coroutine\Channel;
use Swoole\Coroutine;
use Swoole\WebSocket\Frame;

use function Swoole\Coroutine\run;

run(function () {
    $server = new Server('0.0.0.0', 9502, false);
    $server->set([
        'open_websocket_ping_frame' => true,
        'open_websocket_pong_frame' => false,
    ]);
    $message = <<<EOF

    web UI:  listen http://0.0.0.0:9502

EOF;
    printf($message);

    $server->handle('/', function (Request $request, Response $response) {
        //https://developer.mozilla.org/zh-CN/docs/Web/HTTP/Basics_of_HTTP/MIME_types

        $request_uri = str_replace('..', '', $request->server['request_uri']);
        $request_uri = str_replace('//', '/', $request_uri);
        $urlinfo = parse_url($request_uri);
        $path = empty($urlinfo['path']) ? "/" : $urlinfo['path'];

        if ($request_uri == '/') {
            $path = '/index.html';
        }
        //$file = realpath(__DIR__ . '/public/') . $path;
        $file = __DIR__ . '/public/' . $path;
        echo __DIR__ . PHP_EOL;
        echo $file;
        echo PHP_EOL;

        //printf("%s,%s%s", $path, $file,PHP_EOL);
        $result = [];

        if (is_file($file)) {
            if (str_ends_with($request_uri, '.js')) {
                $mimetype = 'application/javascript;charset=utf-8';
            } elseif (str_ends_with($request_uri, 'css')) {
                $mimetype = 'text/css;charset=utf-8';
            } elseif (str_ends_with($request_uri, 'html')) {
                $mimetype = 'text/html;charset=utf-8';
            } else {
                $finfo = finfo_open(FILEINFO_MIME);
                $mimetype = finfo_file($finfo, $file);
                finfo_close($finfo);
                if (!$mimetype) {
                    $mimetype = 'text/plain;charset=utf-8';
                }
            }
            $response->header('content-type', $mimetype);
            $response->end(file_get_contents($file));
        } else {
            $response->status(404);
        }
    });

    $server->handle('/api', function (Request $request, Response $response) {

        $response->header('Content-Type', 'application/json; charset=utf-8');
        $response->header('access-control-allow-credentials', 'true');

        $response->header('access-control-allow-methods', 'GET,HEAD,POST,OPTIONS');
        $response->header('access-control-allow-headers', 'content-type,Authorization');
        $response->header('Access-Control-Allow-Private-Network', 'true');
        $origin = empty($request->header['origin']) ? '*' : $request->header['origin'];
        $response->header('access-control-allow-origin', $origin);
        $request_method = empty($request->header['request_method']) ? '' : $request->header['request_method'];
        if ($request_method == "OPTIONS") {
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

        //var_dump($action);
        $parameter = $request->getContent();
        $parameter = json_decode($parameter, true);
        //var_dump($parameter);

        $word_dir = realpath(__DIR__ . '/../../');
        if ($action === 'changeBranchAction') {
            $branch_name = $parameter['data']['branch_name'];
            $cmd = <<<EOF
         cd $word_dir
         echo "将分支切换为：$branch_name"
         # git checkout $branch_name

EOF;
            ob_start();
            passthru($cmd, $result_code);
            $result = ob_get_contents();
            ob_end_clean();
            $result = trim($result);
            echo $result;
        } elseif ($action === 'branchListAction') {
            $cmd = <<<EOF
         cd $word_dir
         git branch | cat

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
            // var_dump($result);
        } elseif ($action === 'extensionListAction') {
            $cmd = <<<EOF
            cd $word_dir/sapi/src/builder/extension
            ls -A .

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
        } elseif ($action === 'workflowAction') {
            $conf = yaml_parse_file(__DIR__ . DIRECTORY_SEPARATOR . 'workflow.yaml');
            $result = $conf;
        }
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
        //websocket  控制帧 ping,pong
        //The Ping frame contains an opcode of 0x9.
        //The Pong frame contains an opcode of 0xA.
        //chrome是实现了ping/pong的，只要服务端发送了ping, 那么会立即收到一个pong
        //https://wiki.swoole.com/#/websocket_server?id=%E5%8F%91%E9%80%81ping%E5%B8%A7
        //https://wiki.swoole.com/#/websocket_server?id=%e6%95%b0%e6%8d%ae%e5%b8%a7%e7%b1%bb%e5%9e%8b


        //处理工作流
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
                    //参考 :
                    // https://wiki.swoole.com/#/websocket_server?id=%e6%95%b0%e6%8d%ae%e5%b8%a7%e7%b1%bb%e5%9e%8b
                    /*
                    if ($frame->opcode == 0x09) {
                        echo "Ping frame received: Code {$frame->opcode}\n";
                        // 回复 Pong 帧
                        $pongFrame = new Swoole\WebSocket\Frame;
                        $pongFrame->opcode = WEBSOCKET_OPCODE_PONG;
                        $ws->push($frame->fd, $pongFrame);
                    }
                    */


                    // WEBSOCKET_OPCODE_PONG 值为 0xa
                    if ($frame->opcode == 0xa) {
                        echo "Pong frame received: Code {$frame->opcode}\n";
                    } else {
                        Swoole\Timer::tick(30 * 1000, function () use ($ws) {
                            $time = date("c");
                            echo " [{$time}] server ping\n";
                            $pingFrame = new Frame();
                            $pingFrame->opcode = WEBSOCKET_OPCODE_PING;
                            $ws->push($pingFrame);
                            $pingFrame = null;
                        });
                    }

                    if ($frame->data == 'close' || get_class($frame) === CloseFrame::class) {
                        $ws->close();
                        break;
                    }
                    $request = json_decode($frame->data, true);
                    if (isset($request['action'])) {
                        $data = [
                            "code" => 200,
                            "data" => [],
                            "msg" => 'ok'

                        ];
                        $action = $request['action'];
                        $cmd = isset($request['data']) ? $request['data'] : [];
                        switch ($action) {
                            case 'get_instance_state':
                                $data['data'] = [
                                    'instance_id' => 1,
                                    'state' => 'init'
                                ];
                                break;
                            default:
                                break;
                        }

                        $ws->push(json_encode($data));
                    }
                }
            }
        }
    });

    $server->handle('/stop', function ($request, $response) use ($server) {
        $response->end("<h1>Stop</h1>");
        $server->shutdown();
    });

    $server->start();
});

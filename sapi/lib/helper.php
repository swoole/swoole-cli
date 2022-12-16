<?php
const DEFAULT_URL = 'https://www.swoole.com/download?out=json&limit=20';

function swoole_cli_self_update()
{
    $url = getenv('SWOOLE_CLI_DOWNLOAD_URL') ?: DEFAULT_URL;
    $_ = $_ENV['_'];
    $binFile = $_[0] == '/' ? $_ : realpath($_ENV['PWD'] . '/' . $_);
    $list = file_get_contents($url, false, stream_context_create(['http' => ['timeout' => 30,]]));
    if (!$list) {
        echo "Failed to get version list, URL=" . $url . "\n";
        return;
    }
    $json = json_decode($list);
    $uname = php_uname();
    if (strstr($uname, 'x86_64') !== false) {
        $arch = 'x64';
    } elseif (strstr($uname, 'aarch64') !== false) {
        $arch = 'aarch64';
    } else {
        echo "unsupported architecture\n";
        return;
    }

    $newVersion = false;
    foreach ($json as $u) {
        if (!preg_match('#^swoole-cli-v(\d+\.\d+\.\d+)-(\S+)-(\S+)\.tar\.xz$#i', $u->filename, $match)) {
            continue;
        }
        $cmp_result = version_compare(SWOOLE_VERSION, $match[1]);
        if ($cmp_result == -1 and $match[3] == $arch and $match[2] == strtolower(PHP_OS)) {
            $newVersion = $u;
            break;
        }
    }

    if ($newVersion === false) {
        echo "The current version `v" . SWOOLE_VERSION . "-{$arch}` is already the latest\n";
    } else {
        echo "Upgrading to version v{$match[1]}\n";
        $tmpFile = "/tmp/{$newVersion->filename}";
        echo `wget -O {$tmpFile} {$newVersion->url}`, PHP_EOL;
        if (!is_file($tmpFile) or filesize($tmpFile) !== intval($newVersion->size)) {
            echo "Failed to download {$newVersion->url}\n";
            return;
        }
        echo `cd /tmp && tar xvf {$newVersion->filename}`, PHP_EOL;

        $tmpBinFile = '/tmp/swoole-cli';
        if (!is_file($tmpBinFile) or filesize($tmpBinFile) != 0) {
            echo "Failed to decompress archive {$newVersion->filename}\n";
            return;
        }
        echo `mv /tmp/swoole-cli $binFile`;
    }
}

<?php

declare(strict_types=1);

namespace SwooleCli\PreprocessorTrait;

trait DownloadBoxTrait
{
    protected function generateLibraryDownloadLinks(): void
    {
        $this->mkdirIfNotExists($this->getRootDir() . '/var/', 0755, true);
        $download_urls = [];
        foreach ($this->libraryList as $item) {
            if (empty($item->url) || $item->enableDownloadScript) {
                continue;
            }
            $url = '';
            $item->mirrorUrls[] = $item->url;
            if (!empty($item->mirrorUrls)) {
                $newMirrorUrls = [];
                foreach ($item->mirrorUrls as $value) {
                    $newMirrorUrls[] = trim($value);
                }
                $url = implode("\t", $newMirrorUrls);
            }
            $download_urls[] = $url . PHP_EOL . " out=" . $item->file;
        }
        file_put_contents($this->getRootDir() . '/var/download_library_urls.txt', implode(PHP_EOL, $download_urls));

        $download_urls = [];
        foreach ($this->extensionMap as $item) {
            if (!empty($item->peclVersion)) {
                $item->file = $item->name . '-' . $item->peclVersion . '.tgz';
                $item->path = $this->extensionDir . '/' . $item->file;
                $item->url = "https://pecl.php.net/get/{$item->file}";
                $download_urls[] = $item->url . PHP_EOL . " out=" . $item->file;
            } elseif ($item->enableDownloadScript && !empty($item->url)) {
                if (empty($item->file)) {
                    $item->file = $item->name . '.tgz';
                }
                $item->path = $this->extensionDir . '/' . $item->file;
                $download_urls[] = $item->url . PHP_EOL . " out=" . $item->file;
            } else {
                continue;
            }
        }
        file_put_contents($this->getRootDir() . '/var/download_extension_urls.txt', implode(PHP_EOL, $download_urls));

        $shell_cmd_header = <<<'EOF'
#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)

cd ${__DIR__}
mkdir -p ${__DIR__}/var/tmp
mkdir -p ${__DIR__}/libraries
mkdir -p ${__DIR__}/extensions

EOF;

        $download_scripts = [];
        foreach ($this->libraryList as $item) {
            if (!$item->enableDownloadScript) {
                continue;
            }
            if (empty($item->file)) {
                $item->file = $item->name . '.tar.gz';
            }
            $cacheDir = '${__DIR__}/var/tmp/download/lib';
            $workDir = '${__DIR__}/var';
            $downloadScript = <<<EOF
                test -d {$cacheDir} && rm -rf {$cacheDir}
                mkdir -p {$cacheDir}
                cd {$cacheDir}
                test -d {$item->downloadDirName} && rm -rf {$item->downloadDirName}
                {$item->downloadScript}
                cd {$item->downloadDirName}
                test -f {$workDir}/libraries/{$item->file} || tar  -czf {$workDir}/{$item->file} ./
                cp -f {$workDir}/{$item->file} "\${__DIR__}/libraries/"
                cd {$workDir}
EOF;

            $download_scripts[] = $downloadScript . PHP_EOL;
        }
        file_put_contents(
            $this->rootDir . '/var/download_library_use_git.sh',
            $shell_cmd_header . PHP_EOL . implode(PHP_EOL, $download_scripts)
        );
        $download_scripts = [];
        foreach ($this->extensionMap as $item) {
            if (!$item->enableDownloadScript) {
                continue;
            }
            if (!empty($item->url)) {
                continue;
            }
            if (empty($item->file)) {
                $item->file = $item->name . '.tgz';
            }
            $cacheDir = '${__DIR__}/var/tmp/download/ext';
            $workDir = '${__DIR__}/var';
            $downloadScript = <<<EOF
                test -d {$cacheDir} && rm -rf {$cacheDir}
                mkdir -p {$cacheDir}
                cd {$cacheDir}
                test -d {$item->downloadDirName} && rm -rf {$item->downloadDirName}
                {$item->downloadScript}
                cd {$item->downloadDirName}
                test -f {$workDir}/extensions/{$item->file} || tar -czf  {$workDir}/{$item->file} ./
                cp -f {$workDir}/{$item->file} "\${__DIR__}/extensions/"
                cd {$workDir}

EOF;

            $download_scripts[] = $downloadScript . PHP_EOL;
        }
        file_put_contents(
            $this->getRootDir() . '/var/download_extension_use_git.sh',
            $shell_cmd_header . PHP_EOL . implode(PHP_EOL, $download_scripts)
        );
    }
}

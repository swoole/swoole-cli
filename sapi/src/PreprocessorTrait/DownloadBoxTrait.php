<?php

declare(strict_types=1);

namespace SwooleCli\PreprocessorTrait;

trait DownloadBoxTrait
{
    public string $downloadScriptHeader = <<<'EOF'
#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)

cd ${__DIR__}
mkdir -p ${__DIR__}/tmp
mkdir -p ${__DIR__}/lib
mkdir -p ${__DIR__}/ext


EOF;

    protected function generateDownloadLinks(): void
    {
        $this->mkdirIfNotExists($this->getRootDir() . '/var/download-box/', 0755, true);

        $download_urls = [];
        foreach ($this->extensionMap as $item) {
            echo $item->name . PHP_EOL;

            if ((!empty($item->peclVersion) || !empty($item->url)) || $item->enableDownloadWithMirrorURL) {
                $download_urls[] = $item->url . PHP_EOL . " out=" . $item->file;
            }
        }
        file_put_contents($this->getRootDir() . '/var/download-box/download_extension_urls.txt', implode(PHP_EOL, $download_urls));

        $download_scripts = [];
        foreach ($this->extensionMap as $item) {
            if ($item->enableDownloadScript && !$item->enableDownloadWithMirrorURL) {
                $workDir = '${__DIR__}/';
                $cacheDir = '${__DIR__}/tmp/ext/' . $item->name;
                $downloadScript = <<<EOF

## ------------------- download extension {$item->name} start -------------------
test -d {$cacheDir} && rm -rf {$cacheDir}
mkdir -p {$cacheDir}
cd {$cacheDir}
{$item->downloadScript}
cd {$item->downloadDirName}
test -f {$workDir}/ext/{$item->file} || tar -czf {$workDir}/ext/{$item->file} ./
cd {$workDir}
## ------------------- download extension {$item->name} end -------------------

EOF;

                $download_scripts[] = $downloadScript . PHP_EOL;
            }
        }
        file_put_contents(
            $this->getRootDir() . '/var/download-box/download_extension_use_git.sh',
            $this->downloadScriptHeader . PHP_EOL .
            implode(PHP_EOL, $download_scripts)
        );

        $download_urls = [];
        foreach ($this->libraryList as $item) {
            if ((!empty($item->url) && !$item->enableDownloadScript) || $item->enableDownloadWithMirrorURL) {
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
        }
        file_put_contents($this->getRootDir() . '/var/download-box/download_library_urls.txt', implode(PHP_EOL, $download_urls));


        $download_scripts = [];
        foreach ($this->libraryList as $item) {
            if ($item->enableDownloadScript && !$item->enableDownloadWithMirrorURL) {
                $workDir = '${__DIR__}/';
                $cacheDir = '${__DIR__}/tmp/lib/' . $item->name;
                $downloadScript = <<<EOF

## ------------------- download library {$item->name} start -------------------
test -d {$cacheDir} && rm -rf {$cacheDir}
mkdir -p {$cacheDir}
cd {$cacheDir}
{$item->downloadScript}
cd {$item->downloadDirName}
test -f  {$workDir}/lib/{$item->file} || tar  -czf {$workDir}/lib/{$item->file} ./
cd {$workDir}
## ------------------- download library {$item->name} end -------------------

EOF;

                $download_scripts[] = $downloadScript . PHP_EOL;
            }
        }
        file_put_contents(
            $this->rootDir . '/var/download-box/download_library_use_git.sh',
            $this->downloadScriptHeader . PHP_EOL .
            implode(PHP_EOL, $download_scripts)
        );
    }
}

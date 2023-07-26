<?php

declare(strict_types=1);

namespace SwooleCli\PreprocessorTrait;

trait DownloadBoxTrait
{
    public $downloadScriptHeader =<<<'EOF'
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

    protected function generateLibraryDownloadLinks(): void
    {
        $this->mkdirIfNotExists($this->getRootDir() . '/var/', 0755, true);

        $download_urls = [];
        foreach ($this->extensionMap as $item) {
            if (!empty($item->peclVersion) || $item->enableDownloadWithMirrorURL) {
                $download_urls[] = $item->url . PHP_EOL . " out=" . $item->file;
            }
        }
        file_put_contents($this->getRootDir() . '/var/download_extension_urls.txt', implode(PHP_EOL, $download_urls));

        $download_scripts = [];
        foreach ($this->extensionMap as $item) {
            if ($item->enableDownloadScript && !$item->enableDownloadWithMirrorURL) {
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
        }
        file_put_contents(
            $this->getRootDir() . '/var/download_extension_use_git.sh',
            $this->downloadScriptHeader  . PHP_EOL .
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
        file_put_contents($this->getRootDir() . '/var/download_library_urls.txt', implode(PHP_EOL, $download_urls));


        $download_scripts = [];
        foreach ($this->libraryList as $item) {
            if ($item->enableDownloadScript && !$item->enableDownloadWithMirrorURL) {
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
        }
        file_put_contents(
            $this->rootDir . '/var/download_library_use_git.sh',
            $this->downloadScriptHeader  . PHP_EOL .
            implode(PHP_EOL, $download_scripts)
        );
    }
}

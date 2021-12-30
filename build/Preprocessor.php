<?php

class Preprocessor
{
    protected array $libraryList = [];
    protected array $extensionList = [];
    protected string $rootDir;
    protected string $libraryDir;
    protected int $maxJob = 8;

    function __construct(string $rootPath)
    {
        $this->rootDir = $rootPath;
        $this->libraryDir = $rootPath . '/pool/lib';
    }

    function addLibrary(string $name, string $url, string $configure = '', string $file = '')
    {
        if (empty($file)) {
            $file = basename($url);
        }
        if (!is_file($this->libraryDir . '/' . $file)) {
            echo `wget $url -O {$this->libraryDir}/$file`;
            echo $file;
        } else {
            echo "file cached: " . $file . PHP_EOL;
        }

        $this->libraryList[] = [
            'name' => $name,
            'file' => $file,
            'configure' => $configure,
        ];
    }

    function addExtension(string $name, string $options)
    {
        $this->extensionList[] = [
            'name' => $name,
            'options' => $options,
        ];
    }

    function gen()
    {
        ob_start();
        include __DIR__ . '/make.php';
        file_put_contents($this->rootDir . '/make.sh', ob_get_clean());
    }

    /**
     * make -j {$n}
     * @param int $n
     */
    function setMaxJob(int $n)
    {
        $this->maxJob = $n;
    }
}

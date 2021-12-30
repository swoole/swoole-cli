<?php

class Library
{
    public string $name;
    public string $url;
    public string $configure = '';
    public string $file = '';
    public string $ldflags = '';
    public string $makeOptions = '';

    function __construct(string $name)
    {
        $this->name = $name;
        return $this;
    }

    function withUrl(string $url): static
    {
        $this->url = $url;
        return $this;
    }

    function withFile(string $file): static
    {
        $this->file = $file;
        return $this;
    }

    function withConfigure(string $configure): static
    {
        $this->configure = $configure;
        return $this;
    }

    function withLdflags(string $ldflags): static
    {
        $this->ldflags = $ldflags;
        return $this;
    }

    function withMakeOptions(string $makeOptions) {
        $this->makeOptions = $makeOptions;
        return $this;
    }
}

class Extension
{
    public string $name;
    public string $options = '';

    function __construct(string $name)
    {
        $this->name = $name;
    }

    function withOptions(string $options): static
    {
        $this->options = $options;
        return $this;
    }
}

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

    function addLibrary(Library $lib)
    {
        if (empty($lib->file)) {
            $lib->file = basename($lib->url);
        }
        if (!is_file($this->libraryDir . '/' . $lib->file)) {
            echo `wget {$lib->url} -O {$this->libraryDir}/{$lib->file}`;
            echo $lib->file;
        } else {
            echo "file cached: " . $lib->file . PHP_EOL;
        }

        $this->libraryList[] = $lib;
    }

    function addExtension(Extension $ext)
    {
        $this->extensionList[] = $ext;
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

    function stats()
    {
        echo "extension count: " . count($this->extensionList) . PHP_EOL;
        echo "library count: " . count($this->libraryList) . PHP_EOL;
    }
}

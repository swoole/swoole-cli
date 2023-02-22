<?php

namespace SwooleCli;

use JetBrains\PhpStorm\Pure;
use MJS\TopSort\CircularDependencyException;
use MJS\TopSort\ElementNotFoundException;
use MJS\TopSort\Implementations\StringSort;

abstract class Project
{
    public string $name;
    public string $homePage = '';
    public string $license = '';
    public string $prefix = '';
    public array $deps = [];
    public int $licenseType = self::LICENSE_SPEC;

    const LICENSE_SPEC = 0;
    const LICENSE_APACHE2 = 1;
    const LICENSE_BSD = 2;
    const LICENSE_GPL = 3;
    const LICENSE_LGPL = 4;
    const LICENSE_MIT = 5;
    const LICENSE_PHP = 6;

    function __construct(string $name)
    {
        $this->name = $name;
    }

    function withLicense(string $license, int $licenseType = self::LICENSE_SPEC): static
    {
        $this->license = $license;
        $this->licenseType = $licenseType;
        return $this;
    }

    function withHomePage(string $homePage): static
    {
        $this->homePage = $homePage;
        return $this;
    }

    function depends(string ...$libs): static
    {
        $this->deps += $libs;
        return $this;
    }
}

class Library extends Project
{
    public string $url;
    public string $configure = '';
    public string $file = '';
    public string $ldflags = '';
    public string $makeOptions = '';
    public string $makeVariables = '';
    public string $makeInstallCommand = 'install';

    public string $makeInstallOptions = '';
    public string $beforeInstallScript = '';
    public string $afterInstallScript = '';
    public string $pkgConfig = '';
    public string $pkgName = '';
    public string $prefix = '/usr';

    function withUrl(string $url): static
    {
        $this->url = $url;
        return $this;
    }

    function withPrefix(string $prefix): static
    {
        $this->prefix = $prefix;
        $this->withLdflags('-L' . $prefix . '/lib');
        $this->withPkgConfig($prefix . '/lib/pkgconfig');
        return $this;
    }

    function getPrefix(): string
    {
        return $this->prefix;
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

    function withMakeVariables(string $variables): static
    {
        $this->makeVariables = $variables;
        return $this;
    }

    function withMakeOptions(string $makeOptions): static
    {
        $this->makeOptions = $makeOptions;
        return $this;
    }

    function withScriptBeforeInstall(string $script)
    {
        $this->beforeInstallScript = $script;
        return $this;
    }

    function withScriptAfterInstall(string $script)
    {
        $this->afterInstallScript = $script;
        return $this;
    }

    public function withMakeInstallCommand(string $makeInstallCommand): static
    {
        $this->makeInstallCommand = $makeInstallCommand;
        return $this;
    }

    function withMakeInstallOptions(string $makeInstallOptions): static
    {
        $this->makeInstallOptions = $makeInstallOptions;
        return $this;
    }

    function withPkgConfig(string $pkgConfig): static
    {
        $this->pkgConfig = $pkgConfig;
        return $this;
    }

    function withPkgName(string $pkgName): static
    {
        $this->pkgName = $pkgName;
        return $this;
    }
}

class Extension extends Project
{
    public string $url;
    public string $options = '';
    public string $peclVersion = '';
    public string $file = '';
    public string $path = '';

    function withOptions(string $options): static
    {
        $this->options = $options;
        return $this;
    }

    function withUrl(string $url): static
    {
        $this->url = $url;
        return $this;
    }

    function withPeclVersion(string $peclVersion): static
    {
        $this->peclVersion = $peclVersion;
        return $this;
    }
}

class Preprocessor
{
    const VERSION = '1.6';
    const IMAGE_NAME = 'phpswoole/swoole-cli-builder';
    const CONTAINER_NAME = 'swoole-cli-builder';

    protected static ?Preprocessor $instance = null;

    protected string $osType = 'linux';
    protected array $libraryList = [];
    protected array $extensionList = [];
    protected array $libraryMap = [];
    protected array $extensionMap = [];
    /**
     * 仅用于预处理阶段
     * @var string
     */
    protected string $rootDir;
    protected string $libraryDir;
    protected string $extensionDir;
    protected array $pkgConfigPaths = [];
    protected string $phpSrcDir;
    protected string $dockerVersion = 'latest';
    /**
     * 指向 swoole-cli 所在的目录，在构建阶段使用
     * $workDir/pool/ext 存放扩展
     * $workDir/pool/lib 存放依赖库
     */
    protected string $workDir = '/work';
    /**
     * 依赖库的构建目录，在构建阶段使用
     * @var string
     */
    protected string $buildDir = '/work/thirdparty';
    /**
     * 编译后.a静态库文件安装目录的全局前缀，在构建阶段使用
     * @var string
     */
    protected string $globalPrefix = '/usr';

    protected string $extraLdflags = '';
    protected string $extraOptions = '';
    protected int $maxJob = 8;
    protected bool $installLibrary = true;
    protected array $inputOptions = [];

    /**
     * Extensions enabled by default
     * @var array|string[]
     */
    protected array $extEnabled = [
        'opcache',
        'curl',
        'iconv',
        'bz2',
        'bcmath',
        'pcntl',
        'filter',
        'session',
        'tokenizer',
        'mbstring',
        'ctype',
        'zlib',
        'zip',
        'posix',
        'sockets',
        'pdo',
        'sqlite3',
        'phar',
        'mysqlnd',
        'mysqli',
        'intl',
        'fileinfo',
        'pdo_mysql',
        'pdo_sqlite',
        'soap',
        'xsl',
        'gmp',
        'exif',
        'sodium',
        'openssl',
        'readline',
        'xml',
        'gd',
        'redis',
        'swoole',
        'yaml',
        'imagick',
        'mongodb',
    ];

    protected array $endCallbacks = [];
    protected array $extCallbacks = [];

    protected function __construct()
    {

    }

    public static function getInstance(): static
    {
        if (!self::$instance) {
            self::$instance = new static;
        }
        return self::$instance;
    }

    protected function setOsType(string $osType)
    {
        $this->osType = $osType;
    }

    function getOsType()
    {
        return $this->osType;
    }

    function getSystemArch()
    {
        $uname = posix_uname();
        switch ($uname['machine']) {
            case 'x86_64':
                return 'x64';
            case 'aarch64':
                return 'arm64';
            default:
                return $uname['machine'];
        }
    }

    function getImageTag(): string
    {
        $arch = $this->getSystemArch();
        if ($arch == 'x64') {
            return self::VERSION;
        } else {
            return self::VERSION . '-' . $arch;
        }
    }

    function setPhpSrcDir(string $phpSrcDir)
    {
        $this->phpSrcDir = $phpSrcDir;
    }


    function setGlobalPrefix(string $prefix)
    {
        $this->globalPrefix = $prefix;
    }

    function getGlobalPrefix(): string
    {
        return $this->globalPrefix;
    }

    function setRootDir(string $rootDir)
    {
        $this->rootDir = $rootDir;
    }

    function getRootDir(): string
    {
        return $this->rootDir;
    }

    function setLibraryDir(string $libraryDir)
    {
        $this->libraryDir = $libraryDir;
    }

    function setExtensionDir(string $extensionDir)
    {
        $this->extensionDir = $extensionDir;
    }

    function setWorkDir(string $workDir)
    {
        $this->workDir = $workDir;
    }

    function setBuildDir(string $buildDir)
    {
        $this->buildDir = $buildDir;
    }

    function getBuildDir() : string
    {
        return $this->buildDir;
    }

    function getWorkDir(): string
    {
        return $this->workDir;
    }

    function setExtraLdflags(string $flags)
    {
        $this->extraLdflags = $flags;
    }

    function setExtraOptions(string $options)
    {
        $this->extraOptions = $options;
    }

    /**
     * make -j {$n}
     * @param int $n
     */
    function setMaxJob(int $n)
    {
        $this->maxJob = $n;
    }

    function donotInstallLibrary()
    {
        $this->installLibrary = false;
    }

    protected function downloadFile(string $url, string $file)
    {
        echo `wget {$url} -O {$file}`;
        if (!is_file($file) or filesize($file) == 0) {
            throw new \RuntimeException("Downloading file[$file] from url[$url] failed");
        }
    }

    function addLibrary(Library $lib)
    {
        if (empty($lib->file)) {
            $lib->file = basename($lib->url);
        }
        $skip_library_download = $this->getInputOption('skip-download');
        if (empty($skip_library_download)) {
            if (!is_file($this->libraryDir . '/' . $lib->file)) {
                echo "[Library] {$lib->file} not found, downloading: " . $lib->url . PHP_EOL;
                $this->downloadFile($lib->url, "{$this->libraryDir}/{$lib->file}");
            } else {
                echo "[Library] file cached: " . $lib->file . PHP_EOL;
            }
        }

        if (!empty($lib->pkgConfig)) {
            $this->pkgConfigPaths[] = $lib->pkgConfig;
        }

        if (empty($lib->license)) {
            throw new \RuntimeException("require license");
        }

        $this->libraryList[] = $lib;
        $this->libraryMap[$lib->name] = $lib;
    }

    function addExtension(Extension $ext)
    {
        if ($ext->peclVersion) {
            $ext->file = $ext->name . '-' . $ext->peclVersion . '.tgz';
            $ext->path = $this->extensionDir . '/' . $ext->file;
            $ext->url = "https://pecl.php.net/get/{$ext->file}";

            if (!is_file($ext->path)) {
                echo "[Extension] {$ext->file} not found, downloading: " . $ext->url . PHP_EOL;
                $this->downloadFile($ext->url, $ext->path);
            } else {
                echo "[Extension] file cached: " . $ext->file . PHP_EOL;
            }

            $dst_dir = "{$this->rootDir}/ext/{$ext->name}";
            if (!is_dir($dst_dir)) {
                echo `mkdir -p $dst_dir`;
            }

            echo `tar --strip-components=1 -C $dst_dir -xf {$ext->path}`;
        }

        $this->extensionList[] = $ext;
        $this->extensionMap[$ext->name] = $ext;
    }

    function getLibrary(string $name): ?Library
    {
        if (!isset($this->libraryMap[$name])) {
            return null;
        }
        return $this->libraryMap[$name];
    }

    function getExtension(string $name): ?Extension
    {
        if (!isset($this->extensionMap[$name])) {
            return null;
        }
        return $this->extensionMap[$name];
    }

    function existsLibrary(string $name): bool
    {
        return isset($this->libraryMap[$name]);
    }

    function existsExtension(string $name): bool
    {
        return isset($this->extensionMap[$name]);
    }

    function addEndCallback($fn)
    {
        $this->endCallbacks[] = $fn;
    }

    function setExtCallback($name, $fn)
    {
        $this->extCallbacks[$name] = $fn;
    }

    function parseArguments(int $argc, array $argv)
    {
        // parse the parameters passed in by the user
        for ($i = 1; $i < $argc; $i++) {
            $arg = $argv[$i];
            $op = $arg[0];
            $value = substr($argv[$i], 1);
            if ($op == '+') {
                $this->extEnabled[] = $value;
            } elseif ($op == '-') {
                if ($arg[1] == '-') {
                    $_ = explode('=', substr($arg, 2));
                    $this->inputOptions[$_[0]] = $_[1] ?? true;
                } else {
                    $key = array_search($value, $this->extEnabled);
                    if ($key !== false) {
                        unset($this->extEnabled[$key]);
                    }
                }
            } elseif ($op == '@') {
                $this->setOsType($value);
            }
        }
    }

    function getInputOption(string $key, mixed $default = false): mixed
    {
        return $this->inputOptions[$key] ?? $default;
    }

    /**
     * @throws CircularDependencyException
     * @throws ElementNotFoundException
     */
    protected function sortLibrary(): void
    {
        $libs = [];
        $sorter = new StringSort();
        foreach ($this->libraryList as $item) {
            $libs[$item->name] = $item;
            $sorter->add($item->name, $item->deps);
        }
        $sorted_list = $sorter->sort();
        foreach ($this->extensionList as $item) {
            if ($item->deps) {
                foreach ($item->deps as $lib) {
                    if (!isset($libs[$lib])) {
                        throw new \RuntimeException("The ext-{$item->name} depends on $lib, but it does not exist");
                    }
                }
            }
        }

        $libraryList = [];
        foreach ($sorted_list as $name) {
            $libraryList[] = $libs[$name];
        }
        $this->libraryList = $libraryList;
    }

    /**
     * Scan and load config files in directory
     */
    protected function scanConfigFiles(string $dir, array &$extAvailabled)
    {
        $files = scandir($dir);
        foreach ($files as $f) {
            if ($f == '.' or $f == '..' or substr($f, -4, 4) != '.php') {
                continue;
            }
            $path = $dir . '/' . $f;
            if (is_dir($path)) {
                $this->scanConfigFiles($path, $extAvailabled);
            } else {
                $extAvailabled[basename($f, '.php')] = require $path;
            }
        }
    }

    /**
     * @throws CircularDependencyException
     * @throws ElementNotFoundException
     */
    function execute()
    {
        if (empty($this->rootDir)) {
            $this->rootDir = dirname(__DIR__);
        }
        if (empty($this->libraryDir)) {
            $this->libraryDir = $this->rootDir . '/pool/lib';
        }
        if (empty($this->extensionDir)) {
            $this->extensionDir = $this->rootDir . '/pool/ext';
        }
        if (!is_dir($this->libraryDir)) {
            mkdir($this->libraryDir, 0777, true);
        }
        if (!is_dir($this->extensionDir)) {
            mkdir($this->extensionDir, 0777, true);
        }
        if (empty($this->osType)) {
            switch (PHP_OS) {
                default:
                case 'Linux':
                    $this->setOsType('linux');
                    break;
                case 'Darwin':
                    $this->setOsType('macos');
                    break;
                case 'WINNT':
                    $this->setOsType('win');
                    break;
            }
        }

        include __DIR__ . '/constants.php';

        $extAvailabled = [];
        if (is_dir($this->rootDir . '/conf.d')) {
            $this->scanConfigFiles($this->rootDir . '/conf.d', $extAvailabled);
        }
        $confPath = $this->getInputOption('conf-path');
        if ($confPath) {
            $confDirList = explode(':', $confPath);
            foreach ($confDirList as $dir) {
                if (!is_dir($dir)) {
                    continue;
                }
                $this->scanConfigFiles($dir, $extAvailabled);
            }
        }

        $this->extEnabled = array_unique($this->extEnabled);
        foreach ($this->extEnabled as $ext) {
            if (!isset($extAvailabled[$ext])) {
                echo "unsupported extension[$ext]\n";
                continue;
            }
            ($extAvailabled[$ext])($this);
            if (isset($this->extCallbacks[$ext])) {
                ($this->extCallbacks[$ext])($this);
            }
        }

        $this->pkgConfigPaths[] = '$PKG_CONFIG_PATH';
        $this->pkgConfigPaths = array_unique($this->pkgConfigPaths);
        $this->sortLibrary();

        ob_start();
        include __DIR__ . '/make.php';
        file_put_contents($this->rootDir . '/make.sh', ob_get_clean());

        ob_start();
        include __DIR__ . '/license.php';
        if (!$this->rootDir . '/bin') {
            mkdir($this->rootDir . '/bin');
        }
        file_put_contents($this->rootDir . '/bin/LICENSE', ob_get_clean());

        foreach ($this->endCallbacks as $endCallback) {
            $endCallback($this);
        }

        echo '==========================================================' . PHP_EOL;
        echo "Extension count: " . count($this->extensionList) . PHP_EOL;
        echo '==========================================================' . PHP_EOL;
        foreach ($this->extensionList as $item) {
            echo $item->name . PHP_EOL;
        }

        echo '==========================================================' . PHP_EOL;
        echo "Library count: " . count($this->libraryList) . PHP_EOL;
        echo '==========================================================' . PHP_EOL;
        foreach ($this->libraryList as $item) {
            echo "{$item->name}\n";
        }
    }
}

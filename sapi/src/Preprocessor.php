<?php

namespace SwooleCli;

use AllowDynamicProperties;
use MJS\TopSort\CircularDependencyException;
use MJS\TopSort\ElementNotFoundException;
use MJS\TopSort\Implementations\StringSort;
use RuntimeException;
use SwooleCli\PreprocessorTrait\DownloadBoxTrait;
use SwooleCli\PreprocessorTrait\WebUITrait;

#[AllowDynamicProperties]
class Preprocessor
{
    use DownloadBoxTrait;

    use WebUITrait;

    public const VERSION = '1.7';

    public const IMAGE_NAME = 'phpswoole/swoole-cli-builder';
    public const CONTAINER_NAME = 'swoole-cli-builder';

    protected static ?Preprocessor $instance = null;

    protected array $prepareArgs = [];
    protected string $osType = 'linux';
    protected array $libraryList = [];
    protected array $extensionList = [];

    protected string $cCompiler = 'clang';
    protected string $cppCompiler = 'clang++';

    protected string $lld = 'ld.lld';

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
    protected array $nfpmDepends = [];
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
    protected string $globalPrefix = '/usr/local/swoole-cli';

    protected string $extraLdflags = '';

    protected string $extraOptions = '';
    protected string $extraCflags = '';

    protected array $variables = [];

    protected array $exportVariables = [];

    protected array $preInstallCommands = [
        'alpine' => [],
        'debian' => [],
        'ubuntu' => [],
        'macos' => []
    ];

    /**
     * default value : CPU   logical processors
     * @var string
     */
    protected string $maxJob = '${LOGICAL_PROCESSORS}';

    /**
     * CPU   logical processors
     * @var string
     */
    protected string $logicalProcessors = '';

    protected array $inputOptions = [];

    protected array $binPaths = [];

    /**
     * Extensions enabled by default
     * @var array|string[]
     */
    protected array $extEnabled;

    protected array $extEnabledBuff = [];

    protected array $endCallbacks = [];
    protected array $extCallbacks = [];
    protected array $beforeConfigure = [];

    protected string $configureVariables;

    protected string $buildType = 'release';
    protected bool $inVirtualMachine = false;
    protected bool $skipHashVerify = false;

    protected string $proxyConfig = '';

    protected string $httpProxy = '';

    protected string $gitProxyConfig = '';

    protected function __construct()
    {
        $this->setOsType($this->getRealOsType());
        $this->extEnabled = require __DIR__ . '/builder/enabled_extensions.php';
    }

    public function setLinker(string $ld): static
    {
        $this->lld = $ld;
        return $this;
    }

    public static function getInstance(): static
    {
        if (!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    protected function setOsType(string $osType): void
    {
        $this->osType = $osType;
    }

    public function getOsType(): string
    {
        return $this->osType;
    }

    public function getSystemArch(): string
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

    public function getDebArch(): string
    {
        $uname = posix_uname();
        switch ($uname['machine']) {
            case 'x86_64':
                return 'amd64';
            case 'aarch64':
                return 'arm64';
            default:
                return $uname['machine'];
        }
    }

    public function getImageTag(): string
    {
        $arch = $this->getSystemArch();
        if ($arch == 'x64') {
            return self::VERSION;
        } else {
            return self::VERSION . '-' . $arch;
        }
    }

    public function getBaseImageTag(): string
    {
        $arch = $this->getSystemArch();
        if ($arch == 'x64') {
            return 'base';
        } else {
            return 'base' . '-' . $arch;
        }
    }

    public function setPhpSrcDir(string $phpSrcDir)
    {
        $this->phpSrcDir = $phpSrcDir;
    }

    public function getPhpSrcDir(): string
    {
        return $this->phpSrcDir;
    }


    public function setGlobalPrefix(string $prefix): void
    {
        $this->globalPrefix = $prefix;
    }

    public function getGlobalPrefix(): string
    {
        return $this->globalPrefix;
    }

    public function setRootDir(string $rootDir)
    {
        $this->rootDir = $rootDir;
    }

    public function getRootDir(): string
    {
        return $this->rootDir;
    }

    public function getPrepareArgs(): array
    {
        return $this->prepareArgs;
    }

    public function setLibraryDir(string $libraryDir)
    {
        $this->libraryDir = $libraryDir;
    }

    public function setExtensionDir(string $extensionDir)
    {
        $this->extensionDir = $extensionDir;
    }

    public function setWorkDir(string $workDir)
    {
        $this->workDir = $workDir;
    }

    public function setBuildDir(string $buildDir)
    {
        $this->buildDir = $buildDir;
    }

    public function getBuildDir(): string
    {
        return $this->buildDir;
    }

    public function getSwooleVersion(): string
    {
        return trim(file_get_contents($this->rootDir . '/sapi/SWOOLE-VERSION.conf'));
    }

    public function getWorkDir(): string
    {
        return $this->workDir;
    }

    public function getWorkExtDir(): string
    {
        return $this->workDir . '/ext/';
    }

    public function setExtraLdflags(string $flags)
    {
        $this->extraLdflags = $flags;
    }

    public function setExtraCflags(string $flags)
    {
        $this->extraCflags = $flags;
    }

    public function setConfigureVariables(string $variables): void
    {
        $this->configureVariables = $variables;
    }

    public function setExtraOptions(string $options): void
    {
        $this->extraOptions = $options;
    }

    /**
     * make -j {$n}
     * @param int $n
     * @return Preprocessor
     */
    public function setMaxJob(int $n): static
    {
        $this->maxJob = (string)$n;
        return $this;
    }

    public function getMaxJob(): string
    {
        return $this->maxJob;
    }

    /**
     * set CPU  logical processors
     * @param string $logicalProcessors
     * @return $this
     */
    public function setLogicalProcessors(string $logicalProcessors): static
    {
        $this->logicalProcessors = $logicalProcessors;
        return $this;
    }

    /**
     * @param string $buildType 构建类型 [ release | dev | debug ]
     * @return $this
     *
     */
    public function setBuildType(string $buildType): static
    {
        $this->buildType = $buildType;
        return $this;
    }

    public function getBuildType(): string
    {
        return $this->buildType;
    }

    /**
     * 生成代理配置
     * @param string $shell (http_proxy 代理配置 + no_proxy配置 )
     * @param string $httpProxy (http 代理配置 )
     * @return $this
     */
    public function setProxyConfig(string $shell = '', string $httpProxy = ''): static
    {
        $this->proxyConfig = $shell;
        $this->httpProxy = $httpProxy;
        $proxyInfo = parse_url($httpProxy);
        if (!empty($proxyInfo['scheme']) && !empty($proxyInfo['host']) && !empty($proxyInfo['port'])) {
            $proto = '';
            $socat_proxy_proto = '';

            switch (strtolower($proxyInfo['scheme'])) {
                case 'socks5':
                case "socks5h":
                    $proto = 5;
                    $socat_proxy_proto = 'socks4a';
                    break;
                case "socks4a":
                case 'socks4':
                    $proto = 4;
                    $socat_proxy_proto = 'socks4a';
                    break;
                default:
                    $proto = "connect";
                    $socat_proxy_proto = 'proxy';
                    break;
            }

            /*
             * sockat 代理例子
             * http://www.dest-unreach.org/socat/doc/socat.html
             * socat - socks4a:<socks-server>:%h:%p,socksport=2000
             * socat - proxy:<proxy-server>:%h:%p,proxyport=2000
             */

            $socat_proxy_cmd = '';
            if ($socat_proxy_proto == 'socks4a') {
                $socat_proxy_cmd = "socat - socks4a:{$proxyInfo['host']}:\\$1:\\$2,socksport={$proxyInfo['port']}";
            } else {
                $socat_proxy_cmd = "socat - proxy:{$proxyInfo['host']}:\\$1:\\$2,proxyport={$proxyInfo['port']}";
            }

            $this->gitProxyConfig = <<<__GIT_PROXY_CONFIG_EOF
export GIT_PROXY_COMMAND=/tmp/git-proxy;

cat  > \$GIT_PROXY_COMMAND <<___EOF___
#!/bin/bash

# macos环境下 nc 不可用, 使用 socat 代替
# nc -X {$proto}  -x {$proxyInfo['host']}:{$proxyInfo['port']} "\\$1" "\\$2"

{$socat_proxy_cmd};

___EOF___

chmod +x \$GIT_PROXY_COMMAND;

__GIT_PROXY_CONFIG_EOF;
        }

        return $this;
    }

    public function getProxyConfig(): string
    {
        return $this->proxyConfig;
    }

    public function getHttpProxy(): string
    {
        return $this->httpProxy;
    }

    public function getGitProxyConfig(): string
    {
        return $this->gitProxyConfig;
    }


    public function setExtEnabled(array $extEnabled = []): static
    {
        $this->extEnabled = array_merge($this->extEnabledBuff, $extEnabled);
        return $this;
    }


    public function doNotInstallLibrary(): void
    {
        $this->installLibrary = false;
    }

    /**
     * @param string $url
     * @param string $file
     * @param object|null $project [ $lib or $ext ]
     * @param string $httpProxyConfig
     * @return void
     */
    protected function downloadFile(?object $project = null, string $httpProxyConfig = ''): void
    {
        $url = $project->url;
        $file = $project->path;
        $retry_number = DOWNLOAD_FILE_RETRY_NUMBE;
        $wait_retry = DOWNLOAD_FILE_WAIT_RETRY;
        $connect_timeout = DOWNLOAD_FILE_CONNECTION_TIMEOUT;
        echo PHP_EOL;
        if ($this->getInputOption('with-downloader') === 'wget') {
            $cmd = "wget   {$url}  -O {$file}  -t {$retry_number} --wait={$wait_retry} -T {$connect_timeout} ";
        } else {
            $cmd = "curl  --connect-timeout {$connect_timeout} --retry {$retry_number}  --retry-delay {$wait_retry}  -fSLo '{$file}' '{$url}' ";
        }
        $cmd = $httpProxyConfig . PHP_EOL . $cmd;
        echo $cmd;
        echo PHP_EOL;
        echo `$cmd`;
        echo PHP_EOL;
    }

    /**
     * @param object $project
     * @param string $httpProxyConfig
     * @return void
     */
    public function downloadFileWithPie(?object $project = null, string $httpProxyConfig): void
    {
        $pieName = $project->pieName;
        $pieVersion = $project->pieVersion;
        $file = $project->file;
        $path = $project->path;

        $workdir = $this->getWorkDir();
        $cmd = <<<EOF
test -f {$workdir}/runtime/php/php && export PATH={$workdir}/runtime/php/:\$PATH ;
{$httpProxyConfig}
export PIE_WORKING_DIRECTORY={$workdir}/var/ext/pie/
test -d \$PIE_WORKING_DIRECTORY || mkdir -p \$PIE_WORKING_DIRECTORY ;
cd {$workdir}/var/ext/
TEMP_FILE=$(mktemp) && echo "TEMP_FILE: \${TEMP_FILE}" ;
{ pie download {$pieName}:{$pieVersion} ; } > \${TEMP_FILE} 2>&1
cat \${TEMP_FILE}
SOURCE_CODE_DIR=\$(cat \${TEMP_FILE} | grep 'source to: ' | awk -F 'source to: ' '{ print $2 }')
rm -f \${TEMP_FILE}
echo "{$pieName}:{$pieVersion} source code: \${SOURCE_CODE_DIR}"
pie info {$pieName}:{$pieVersion};
cd \${SOURCE_CODE_DIR}
tar -czf "{$workdir}/var/ext/{$file}" .
cp -f {$workdir}/var/ext/{$file} {$path}
cd {$workdir}
EOF;
        echo $cmd;
        echo PHP_EOL;
        echo '------------RUNNING START-------------';
        echo PHP_EOL;
        echo `$cmd`;
        echo '------------RUNNING   END-------------';
        echo PHP_EOL;
    }

    /**
     * @param object|null $project
     * @param string $httpProxyConfig
     * @return void
     */
    protected function downloadFileWithScript(?object $project = null, string $httpProxyConfig): void
    {

        if (!empty($project->downloadScript) && !empty($project->downloadDirName)) {
            $workDir = $this->getRootDir();
            $cacheDir = '${__DIR__}/var/tmp/' . $project->name;
            $downloadScript = <<<EOF
                            __DIR__={$workDir}/
                            {$httpProxyConfig}
                            test -d {$cacheDir} && rm -rf {$cacheDir}
                            mkdir -p {$cacheDir}
                            cd {$cacheDir}
                            {$project->downloadScript}
                            cd {$project->downloadDirName}
                            test -f {$project->path} ||  tar  -zcf {$project->path} ./
                            cd {$workDir}

EOF;

        } else {
            throw new Exception(
                "[Extension] withDownloadScript() require \$downloadDirName and \$downloadScript  "
            );
        }

        echo PHP_EOL;
        echo $downloadScript;
        echo PHP_EOL;
        echo `$downloadScript`;
        echo PHP_EOL;
    }

    /**
     * @param Library $lib
     * @throws Exception
     */
    public function addLibrary(Library $lib): void
    {
        $downloadType = ""; //curl , download_script
        if ($lib->enableDownloadScript) {
            $downloadType = "download_script";
        }
        if (!empty($lib->url)) {
            $downloadType = "curl";
        }
        if (!empty($downloadType)) {
            if (empty($lib->file)) {
                if ($lib->enableDownloadScript) {
                    $lib->file = $lib->name . '.tar.gz';
                } else {
                    $lib->file = basename($lib->url);
                }
            }

            if (!empty($this->getInputOption('with-download-mirror-url'))) {
                if ($lib->enableDownloadWithOriginURL === false) {
                    $lib->url = $this->getInputOption('with-download-mirror-url') . '/lib/' . $lib->file;
                    $lib->enableDownloadWithMirrorURL = true;
                    $downloadType = "curl";
                }
            }

            $lib->path = $this->libraryDir . '/' . $lib->file;
            if ($lib->enableHashVerify) {
                // 本地文件被修改，hash 不一致，删除后重新下载
                $lib->hashVerify($lib->path);
            }

            //文件内容为空
            if (file_exists($lib->path) && (filesize($lib->path) == 0)) {
                unlink($lib->path);
            }

            //获取最新的源码包
            if (is_file($lib->path) && $lib->enableLatestTarball) {
                unlink($lib->path);
            }

            $skip_download = ($this->getInputOption('skip-download'));
            if (!$skip_download) {
                if (file_exists($lib->path)) {
                    echo "[Library] file cached: " . $lib->file . PHP_EOL;
                } else {
                    $httpProxyConfig = $this->getProxyConfig();
                    if ($lib->enableGitProxy) {
                        $httpProxyConfig = $httpProxyConfig . PHP_EOL . $this->getGitProxyConfig();
                    }
                    if (!$lib->enableHttpProxy) {
                        $httpProxyConfig = '';
                    }
                    if ($downloadType == "download_script") {
                        $this->downloadFileWithScript($lib, $httpProxyConfig);
                    } else {
                        echo "[Library] {$lib->file} not found, downloading: " . $lib->url . PHP_EOL;
                        $this->downloadFile($lib, $httpProxyConfig);
                    }

                    $file = $lib->path;
                    if (is_file($file) && (filesize($file) == 0)) {
                        unlink($file);
                    }
                    // 下载失败
                    if (!is_file($file) or filesize($file) == 0) {
                        throw new Exception("Downloading file [" . basename($file) . "] failed");
                    }
                    // 下载文件的 hash 不一致
                    if (!$this->skipHashVerify and $lib->enableHashVerify) {
                        if (!$lib->hashVerify($file)) {
                            throw new Exception("The {$lib->hashAlgo} of downloaded file[$file] is inconsistent with the configuration");
                        }
                    }
                }

                if ($this->getInputOption('show-tarball-hash')) {
                    echo "[Library] {$lib->name} " . PHP_EOL;
                    echo "md5:    " . hash_file('md5', $lib->path) . PHP_EOL;
                    echo "sha1:   " . hash_file('sha1', $lib->path) . PHP_EOL;
                    echo "sha256: " . hash_file('sha256', $lib->path) . PHP_EOL;
                    echo PHP_EOL;
                }
            }
        } else {
            throw new Exception(
                "[Library] require url OR download_script "
            );
        }
        if (!empty($lib->pkgConfig)) {
            $this->pkgConfigPaths[] = $lib->pkgConfig;
        }

        if (!empty($lib->binPath)) {
            if (is_array($lib->binPath)) {
                $this->binPaths = array_merge($this->binPaths, $lib->binPath);
            } else {
                $this->binPaths[] = $lib->binPath;
            }
        }

        if (empty($lib->license)) {
            throw new Exception("require license");
        }

        if (!empty($lib->preInstallCommands)) {
            foreach (['alpine', 'debian', 'ubuntu', 'macos'] as $os) {
                if (!empty($lib->preInstallCommands[$os])) {
                    $this->preInstallCommands[$os] = array_merge(
                        $this->preInstallCommands[$os],
                        $lib->preInstallCommands[$os]
                    );
                }
            }
        }

        $this->libraryList[] = $lib;
        $this->libraryMap[$lib->name] = $lib;
    }

    public function addExtension(Extension $ext): void
    {
        $extensionVersion = "";
        $downloadType = ""; //pecl , pie , curl , download_script

        if (
            !empty($ext->peclVersion) ||
            $ext->enableDownloadScript ||
            !empty($ext->url) ||
            !empty($ext->pieVersion)
        ) {
            if (!empty($ext->peclVersion)) {
                $extensionVersion = $ext->peclVersion;
                $downloadType = "pecl";
                $ext->file = $ext->name . '-' . $extensionVersion . '.tgz';
                $ext->url = "https://pecl.php.net/get/{$ext->file}";
            }
            if (!empty($ext->pieVersion)) {
                $extensionVersion = $ext->pieVersion;
                $downloadType = "pie";
                $ext->file = $ext->name . '-' . $extensionVersion . '.tgz';
                $ext->url = '';
            }

            if (!empty($ext->url)) {
                $downloadType = "curl";
                if (empty($ext->file)) {
                    $ext->file = $ext->name . '.tgz';
                }
            }

            if ($ext->enableDownloadScript) {
                $downloadType = "download_script";
                if (empty($ext->file)) {
                    $ext->file = $ext->name . '.tgz';
                }
                $ext->url = '';
            }

            $ext->path = $this->extensionDir . '/' . $ext->file;

            if (!empty($this->getInputOption('with-download-mirror-url'))) {
                if ($ext->enableDownloadWithOriginURL === false) {
                    $ext->url = $this->getInputOption('with-download-mirror-url') . '/ext/' . $ext->file;
                    $ext->enableDownloadWithMirrorURL = true;
                    $downloadType = "curl";
                }
            }

            if (file_exists($ext->path)) {
                if (!$this->skipHashVerify and $ext->enableHashVerify) {
                    // 检查文件的 hash，若不一致删除后重新下载
                    $ext->hashVerify($ext->path);
                }
                //文件内容为空，重新下载
                if ((filesize($ext->path) == 0)) {
                    unlink($ext->path);
                }
                //不使用缓存包，拉取最新源码包
                if ($ext->enableLatestTarball) {
                    unlink($ext->path);
                }
            }

            if (!$this->getInputOption('skip-download')) {
                if (!file_exists($ext->path)) {
                    $httpProxyConfig = $this->getProxyConfig();
                    if ($ext->enableGitProxy) {
                        $httpProxyConfig = $httpProxyConfig . PHP_EOL . $this->getGitProxyConfig();
                    }
                    if (!$ext->enableHttpProxy) {
                        $httpProxyConfig = '';
                    }
                    switch ($downloadType) { ////pecl , pie , curl , script
                        case "pecl" :
                        case "curl" :
                            echo "[Extension] {$ext->file} not found, downloading: " . $ext->url . PHP_EOL;
                            $this->downloadFile($ext, $httpProxyConfig);
                            break;
                        case "pie" :
                            echo "[Extension] {$ext->file} not found, download with pie.phar " . $ext->homePage . PHP_EOL;
                            $this->downloadFileWithPie($ext, $httpProxyConfig);
                            break;
                        case "download_script" :
                            $this->downloadFileWithScript($ext, $httpProxyConfig);
                            break;
                        default:
                            break;
                    }

                    $file = $ext->path;
                    if (is_file($file) && (filesize($file) == 0)) {
                        unlink($file);
                    }
                    // 下载失败
                    if (!is_file($file) or filesize($file) == 0) {
                        throw new Exception("Downloading file[" . basename($file) . "]  failed");
                    }
                    // 下载文件的 hash 不一致
                    if (!$this->skipHashVerify and $ext->enableHashVerify) {
                        if (!$ext->hashVerify($file)) {
                            throw new Exception("The {$ext->hashAlgo} of downloaded file[$file] is inconsistent with the configuration");
                        }
                    }
                } else {
                    echo "[Extension] file cached: " . $ext->file . PHP_EOL;
                }

                $dst_dir = "{$this->rootDir}/ext/{$ext->name}";
                $ext_name = $ext->name;
                if (!empty($ext->aliasName)) {
                    $dst_dir = "{$this->rootDir}/ext/{$ext->aliasName}";
                    $ext_name = $ext->aliasName;
                }

                if ($this->getInputOption('show-tarball-hash')) {
                    echo "[Extension] {$ext_name} " . PHP_EOL;
                    echo "md5:    " . hash_file('md5', $ext->path) . PHP_EOL;
                    echo "sha1:   " . hash_file('sha1', $ext->path) . PHP_EOL;
                    echo "sha256: " . hash_file('sha256', $ext->path) . PHP_EOL;
                    echo PHP_EOL;
                }

                if (
                    ($ext->enableLatestTarball || !$ext->enableBuildCached)
                    &&
                    (!empty($downloadType))
                ) {
                    $this->deleteDirectoryIfExists($dst_dir);
                }
                $this->mkdirIfNotExists($dst_dir, 0777, true);
                $cached = $dst_dir . '/.completed';
                if (file_exists($cached) && $ext->enableBuildCached) {
                    if (in_array($this->buildType, ['dev', 'debug'])) {
                        echo '[ext/' . $ext_name . '] cached ' . PHP_EOL;
                    }
                } else {
                    echo $cmd = "tar --strip-components=1 -C $dst_dir -xf {$ext->path}";
                    echo PHP_EOL;
                    echo `$cmd`;
                    echo PHP_EOL;
                    if ($ext->enableBuildCached) {
                        touch($cached);
                    }
                }
            }
        }
        $this->extensionList[] = $ext;
        $this->extensionMap[$ext->name] = $ext;
    }

    public function getLibrary(string $name): ?Library
    {
        if (!isset($this->libraryMap[$name])) {
            return null;
        }
        return $this->libraryMap[$name];
    }

    public function getLibraryPackages(): array
    {
        $packages = [];
        /**
         * @var $item Library
         */
        foreach ($this->libraryList as $item) {
            if (!empty($item->pkgNames)) {
                $packages = array_merge($packages, $item->pkgNames);
            }
        }
        return $packages;
    }

    public function withBinPath(string $path): static
    {
        $this->binPaths[] = $path;
        return $this;
    }

    public function withVariable(string $key, string $value): static
    {
        $this->variables[] = [$key => $value];
        return $this;
    }

    public function withExportVariable(string $key, string $value): static
    {
        $this->exportVariables[] = [$key => $value];
        return $this;
    }

    public function withPreInstallCommand(string $os, string $preInstallCommand): static
    {
        if (!empty($os) && in_array($os, ['alpine', 'debian', 'ubuntu', 'macos']) && !empty($preInstallCommand)) {
            $this->preInstallCommands[$os][] = $preInstallCommand;
        }
        return $this;
    }

    protected array $frameworks = [];

    public function withFramework(string $framework): static
    {
        if (!$this->isMacos()) {
            throw new RuntimeException('frameworks only support macOS');
        }
        if (!in_array($framework, $this->frameworks)) {
            $this->frameworks[] = $framework;
        }
        return $this;
    }

    public function getExtension(string $name): ?Extension
    {
        if (!isset($this->extensionMap[$name])) {
            return null;
        }
        return $this->extensionMap[$name];
    }

    public function existsLibrary(string $name): bool
    {
        return isset($this->libraryMap[$name]);
    }

    public function existsExtension(string $name): bool
    {
        return isset($this->extensionMap[$name]);
    }

    public array $extensionDependentLibraryMap = [];

    public array $extensionDependentPackageNameMap = [];

    public array $extensionDependentPackageNames = [];

    public function setExtensionDependency(): void
    {
        $extensionDepsMap = [];
        foreach ($this->extensionList as $extension) {
            if (empty($extension->deps)) {
                $this->extensionDependentLibraryMap[$extension->name] = [];
            } else {
                $extensionDepsMap[$extension->name] = $extension->deps;
            }
        }

        foreach ($extensionDepsMap as $extensionName => $deps) {
            $pkgNames = [];
            foreach ($deps as $libraryName) {
                $packages = [];
                $this->getLibraryDependenciesByName($libraryName, $packages);
                foreach ($packages as $item) {
                    if (empty($item)) {
                        continue;
                    } else {
                        $pkgNames[] = trim($item);
                    }
                }
                $this->extensionDependentLibraryMap[$extensionName][] = $libraryName;
            }
            $this->extensionDependentPackageNameMap[$extensionName] = $pkgNames;
        }
        $pkgNames = [];
        foreach ($this->extensionDependentPackageNameMap as $extensionName => $value) {
            $pkgNames = array_merge($pkgNames, $value);
            $this->extensionDependentPackageNameMap[$extensionName] = array_values(array_unique($value));
        }
        $this->extensionDependentPackageNames = array_values(array_unique($pkgNames));
    }

    private function getLibraryDependenciesByName($libraryName, &$packages): void
    {
        if (!isset($this->libraryMap[$libraryName])) {
            throw new RuntimeException('library ' . $libraryName . ' no found');
        }
        $lib = $this->libraryMap[$libraryName];
        if (!empty($lib->pkgNames)) {
            $packages = array_merge($packages, $lib->pkgNames);
        }
        if (!empty($lib->deps)) {
            foreach ($lib->deps as $name) {
                $this->getLibraryDependenciesByName($name, $packages);
            }
        }
    }

    public function addEndCallback($fn)
    {
        $this->endCallbacks[] = $fn;
    }

    public function setExtCallback($name, $fn)
    {
        $this->extCallbacks[$name] = $fn;
    }

    public function withBeforeConfigureScript($name, $fn): void
    {
        $this->beforeConfigure[$name] = $fn;
    }

    public function parseArguments(int $argc, array $argv): void
    {
        $this->prepareArgs = $argv;
        // parse the parameters passed in by the user
        for ($i = 1; $i < $argc; $i++) {
            $arg = $argv[$i];
            $op = $arg[0];
            $value = substr($argv[$i], 1);
            if ($op == '+') {
                $this->extEnabled[] = $value;
                $this->extEnabledBuff[] = $value;
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
                $this->inVirtualMachine = $value != $this->getRealOsType();
                $this->setOsType($value);
            }
        }
    }

    /**
     * Get the value of an input option, attempting to read from command-line arguments and environment variables,
     * and returning the default value if not set
     * @param string $key
     * @param string $default
     * @return string
     */
    public function getInputOption(string $key, string $default = ''): string
    {
        if (isset($this->inputOptions[$key])) {
            return $this->inputOptions[$key];
        }
        $env = getenv('SWOOLE_CLI_' . str_replace('-', '_', strtoupper($key)));
        if ($env !== false) {
            return $env;
        }
        return $default;
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
                        throw new Exception("The ext-{$item->name} depends on $lib, but it does not exist");
                    }
                }
            }
        }

        $libraryList = [];
        foreach ($sorted_list as $name) {
            if (empty($name)) {
                continue;
            }
            $libraryList[] = $libs[$name];
        }
        $this->libraryList = $libraryList;
    }

    protected function mkdirIfNotExists(string $dir, int $permissions = 0777, bool $recursive = false): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, $permissions, $recursive);
        }
    }

    protected function deleteDirectoryIfExists($path): bool
    {
        try {
            if (file_exists($path)) {
                $iterator = new \DirectoryIterator($path);
                foreach ($iterator as $fileinfo) {
                    if ($fileinfo->isDot()) {
                        continue;
                    }
                    if ($fileinfo->isDir()) {
                        if ($this->deleteDirectoryIfExists($fileinfo->getPathname())) {
                            rmdir($fileinfo->getPathname());
                        }
                    }
                    if ($fileinfo->islink()) {
                        throw new Exception(
                            'file is ' . $fileinfo->getPathname() . ' link ; The real path is ' . $fileinfo->getRealPath()
                        );
                    }
                    if ($fileinfo->isFile()) {
                        unlink($fileinfo->getPathname());
                    }
                }
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return true;
    }

    /**
     * Scan and load config files in directory
     */
    protected function scanConfigFiles(string $dir, array &$extAvailable): void
    {
        $files = scandir($dir);
        foreach ($files as $f) {
            if ($f == '.' or $f == '..' or substr($f, -4, 4) != '.php') {
                continue;
            }
            $path = $dir . '/' . $f;
            if (is_dir($path)) {
                $this->scanConfigFiles($path, $extAvailable);
            } else {
                $extAvailable[basename($f, '.php')] = require $path;
            }
        }
    }

    public function loadDependentExtension($extension_name): void
    {
        if (!isset($this->extensionMap[$extension_name])) {
            $file = realpath(__DIR__ . '/builder/extension/' . $extension_name . '.php');
            if (!is_file($file)) {
                throw new Exception("The ext-$extension_name does not exist");
            }
            $func = require $file;
            $func($this);
        }
        if (isset($this->extensionMap[$extension_name])) {
            $deps = $this->extensionMap[$extension_name]->dependentExtensions;
            if (!empty($deps)) {
                foreach ($deps as $extension_name) {
                    $this->loadDependentExtension($extension_name);
                }
            }
        }
    }

    public function loadDependentLibrary($library_name)
    {
        if (!isset($this->libraryMap[$library_name])) {
            $file = realpath(__DIR__ . '/builder/library/' . $library_name . '.php');
            if (!is_file($file)) {
                throw new Exception("The library-$library_name does not exist");
            }
            $func = require $file;
            $func($this);
        }

        if (isset($this->libraryMap[$library_name])) {
            $deps = $this->libraryMap[$library_name]->deps;
            if (!empty($deps)) {
                foreach ($deps as $library_name) {
                    $this->loadDependentLibrary($library_name);
                }
            }
        }
    }

    public function generateFile(string $templateFile, string $outFile): bool
    {
        if (!is_file($templateFile)) {
            return false;
        }
        ob_start();
        include $templateFile;
        return file_put_contents($outFile, ob_get_clean());
    }

    /**
     * @throws CircularDependencyException
     * @throws ElementNotFoundException
     */
    public function execute(): void
    {
        if (empty($this->rootDir)) {
            $this->rootDir = dirname(__DIR__, 2);
        }
        if (empty($this->libraryDir)) {
            $this->libraryDir = $this->rootDir . '/pool/lib';
        }
        if (empty($this->extensionDir)) {
            $this->extensionDir = $this->rootDir . '/pool/ext';
        }
        $this->mkdirIfNotExists($this->libraryDir, 0777, true);
        $this->mkdirIfNotExists($this->extensionDir, 0777, true);
        include __DIR__ . '/constants.php';


        $extAvailable = [];
        $this->scanConfigFiles(__DIR__ . '/builder/extension', $extAvailable);


        $confPath = $this->getInputOption('conf-path');
        if ($confPath) {
            $confDirList = explode(':', $confPath);
            foreach ($confDirList as $dir) {
                if (!is_dir($dir)) {
                    continue;
                }
                $this->scanConfigFiles($dir, $extAvailable);
            }
        }

        $this->skipHashVerify = boolval($this->getInputOption('skip-hash-verify'));

        $this->extEnabled = array_unique($this->extEnabled);
        foreach ($this->extEnabled as $ext) {
            if (!isset($extAvailable[$ext])) {
                echo "unsupported extension[$ext]\n";
                continue;
            }
            ($extAvailable[$ext])($this);
            if (isset($this->extCallbacks[$ext])) {
                ($this->extCallbacks[$ext])($this);
            }
        }

        // autoload extension depend extension
        foreach ($this->extensionMap as $ext) {
            foreach ($ext->dependentExtensions as $extension_name) {
                $this->loadDependentExtension($extension_name);
            }
        }

        // autoload  library depend library
        foreach ($this->extensionMap as $ext) {
            foreach ($ext->deps as $library_name) {
                $this->loadDependentLibrary($library_name);
            }
        }

        $this->pkgConfigPaths = array_filter(array_unique($this->pkgConfigPaths));

        $packagesArr = $this->getLibraryPackages();
        if (!empty($packagesArr)) {
            $packages = implode(' ', $packagesArr);
            $this->withVariable('PACKAGES', $packages);
            $this->withVariable('CPPFLAGS', '$CPPFLAGS $(pkg-config --cflags-only-I --static $PACKAGES ) ');
            $this->withVariable('LDFLAGS', '$LDFLAGS $(pkg-config --libs-only-L --static $PACKAGES ) ');
            $this->withVariable('LIBS', '$LIBS $(pkg-config --libs-only-l --static $PACKAGES ) ');
        }
        if (!empty($this->varables) || !empty($packagesArr)) {
            $this->withExportVariable('CPPFLAGS', '$CPPFLAGS');
            $this->withExportVariable('LDFLAGS', '$LDFLAGS');
            $this->withExportVariable('LIBS', '$LIBS');
        }
        $this->binPaths[] = '$PATH';
        $this->binPaths = array_filter(array_unique($this->binPaths));
        $this->sortLibrary();
        $this->setExtensionDependency();

        if ($this->getInputOption('skip-download')) {
            $this->generateDownloadLinks();
        }

        $this->generateFile(__DIR__ . '/template/make-install-deps.php', $this->rootDir . '/make-install-deps.sh');
        $this->generateFile(__DIR__ . '/template/make.php', $this->rootDir . '/make.sh');

        $this->generateFile(__DIR__ . '/template/make-env.php', $this->rootDir . '/make-env.sh');
        $this->generateFile(
            __DIR__ . '/template/make-export-variables.php',
            $this->rootDir . '/make-export-variables.sh'
        );

        shell_exec('chmod a+x ' . $this->rootDir . '/make.sh');
        $this->mkdirIfNotExists($this->rootDir . '/bin');
        $this->generateFile(__DIR__ . '/template/license.php', $this->rootDir . '/bin/LICENSE');
        $this->generateFile(__DIR__ . '/template/credits.php', $this->rootDir . '/bin/credits.html');
        $this->generateFile(__DIR__ . '/template/nfpm-yaml.php', $this->rootDir . '/nfpm-pkg.yaml');

        copy($this->rootDir . '/sapi/scripts/pack-sfx.php', $this->rootDir . '/bin/pack-sfx.php');

        if ($this->getInputOption('with-dependency-graph')) {
            $this->generateFile(
                __DIR__ . '/template/extension_dependency_graph.php',
                $this->rootDir . '/bin/ext-dependency-graph.graphviz.dot'
            );
        }
        if ($this->getInputOption('with-web-ui')) {
            $this->generateWebUIData();
        }
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


    public function getRealOsType(): string
    {
        switch (PHP_OS) {
            default:
            case 'Linux':
                return 'linux';
            case 'Darwin':
                return 'macos';
            case 'WINNT':
                return 'win';
        }
    }

    public function isLinux(): bool
    {
        return $this->osType === 'linux';
    }

    public function isMacos(): bool
    {
        return $this->osType === 'macos';
    }

    public function hasLibrary(string $lib): bool
    {
        return isset($this->libraryMap[$lib]);
    }

    public function hasExtension(string $ext): bool
    {
        return isset($this->extensionMap[$ext]);
    }

    public function cleanFile(string $file): bool
    {
        if (is_file($file)) {
            unlink($file);
            return true;
        }
        return false;
    }
}

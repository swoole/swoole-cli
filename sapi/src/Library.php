<?php

namespace SwooleCli;

class Library extends Project
{
    public array $mirrorUrls = [];

    public string $configure = '';

    public string $ldflags = '';

    public string $buildScript = '';

    public string $makeOptions = '';

    public string $makeVariables = '';

    public string $makeInstallCommand = 'install';

    public string $makeInstallOptions = '';

    public string $beforeInstallScript = '';

    public string $afterInstallScript = '';

    public string $pkgConfig = '';

    public array $pkgNames = [];

    public string $prefix = '/usr';

    public string $binPath = '';

    public bool $cleanBuildDirectory = false;

    public bool $cleanPreInstallDirectory = false;

    public string $preInstallDirectory = '';

    public bool $skipBuildLicense = false;

    public bool $skipDownload = false;

    public bool $skipBuildInstall = false;

    public string $untarArchiveCommand = 'tar';

    public string $label = '';

    public string $enablePkgNames = 'yes';

    public bool $enableBuildLibraryCached = true;

    public array $preInstallCommands = [];

    public bool $enableBuildLibraryHttpProxy = false;

    public bool $enableBuildLibraryGitProxy = false;

    public array $os = ['alpine', 'debian', 'ubuntu', 'macos'];


    public function withMirrorUrl(string $url): static
    {
        $this->mirrorUrls[] = $url;
        return $this;
    }

    public function withPrefix(string $prefix): static
    {
        $this->prefix = $prefix;
        $this->withLdflags('-L' . $prefix . '/lib');
        $this->withPkgConfig($prefix . '/lib/pkgconfig');
        return $this;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function withFile(string $file): static
    {
        $this->file = $file;
        return $this;
    }

    public function withBuildScript(string $script): static
    {
        $this->buildScript = $script;
        return $this;
    }

    public function withConfigure(string $configure): static
    {
        $this->configure = $configure;
        return $this;
    }

    public function withLdflags(string $ldflags): static
    {
        $this->ldflags = $ldflags;
        return $this;
    }

    public function withMakeVariables(string $variables): static
    {
        $this->makeVariables = $variables;
        return $this;
    }

    public function withMakeOptions(string $makeOptions): static
    {
        $this->makeOptions = $makeOptions;
        return $this;
    }

    public function withScriptBeforeInstall(string $script): static
    {
        $this->beforeInstallScript = $script;
        return $this;
    }

    public function withScriptAfterInstall(string $script): static
    {
        $this->afterInstallScript = $script;
        return $this;
    }

    public function withMakeInstallCommand(string $makeInstallCommand): static
    {
        $this->makeInstallCommand = $makeInstallCommand;
        return $this;
    }

    public function withMakeInstallOptions(string $makeInstallOptions): static
    {
        $this->makeInstallOptions = $makeInstallOptions;
        return $this;
    }

    public function withPkgConfig(string $pkgConfig): static
    {
        $this->pkgConfig = $pkgConfig;
        return $this;
    }

    public function withPkgName(string $pkgName): static
    {
        $this->pkgNames[] = $pkgName;
        return $this;
    }

    public function withBinPath(string $path): static
    {
        $this->binPath = $path;
        return $this;
    }

    public function withCleanBuildDirectory(bool $cleanBuildDirectory = true): static
    {
        $this->cleanBuildDirectory = $cleanBuildDirectory;
        return $this;
    }

    public function withCleanPreInstallDirectory(string $preInstallDir): static
    {
        if (!empty($preInstallDir) && (str_starts_with($preInstallDir, PHP_CLI_GLOBAL_PREFIX))) {
            if (PHP_CLI_BUILD_TYPE == 'dev') {
                $this->cleanPreInstallDirectory = true;
                $this->preInstallDirectory = $preInstallDir;
            }
        }
        return $this;
    }

    public function disablePkgNames(): static
    {
        $this->enablePkgNames = 'no';
        return $this;
    }

    public function withUntarArchiveCommand(string $command): static
    {
        $this->untarArchiveCommand = $command;
        return $this;
    }


    public function withSkipBuildLicense(): static
    {
        $this->skipBuildLicense = true;
        return $this;
    }

    public function withSkipDownload(): static
    {
        $this->skipDownload = true;
        return $this;
    }

    public function getSkipDownload()
    {
        return $this->skipDownload;
    }

    public function disableDefaultLdflags(): static
    {
        $this->ldflags = '';
        return $this;
    }

    public function withSkipBuildInstall(): static
    {
        $this->skipBuildInstall = true;
        $this->skipBuildLicense = true;
        $this->withBinPath('');
        $this->disableDefaultPkgConfig();
        $this->disablePkgName();
        $this->disableDefaultLdflags();
        return $this;
    }


    public function disableDefaultPkgConfig(): static
    {
        $this->pkgConfig = '';
        return $this;
    }

    public function disablePkgName(): static
    {
        $this->pkgNames = [];
        return $this;
    }

    public function withLabel(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function withPreInstallCommand(string $os, string $preInstallCommand): static
    {
        if (!empty($os) && in_array($os, $this->os) && !empty($preInstallCommand)) {
            $this->preInstallCommands[$os][] = $preInstallCommand;
        }
        return $this;
    }

    public function withBuildLibraryHttpProxy(
        bool $enableBuildLibraryHttpProxy = true,
        bool $enableBuildLibraryGitProxy = false
    ): static {
        $this->enableBuildLibraryHttpProxy = $enableBuildLibraryHttpProxy;
        $this->enableBuildLibraryGitProxy = $enableBuildLibraryGitProxy;
        return $this;
    }

    public bool $enableSystemOriginEnvPath = false;

    public function withSystemEnvPath(): static
    {
        $this->enableSystemOriginEnvPath = true;
        return $this;
    }

    public bool $enableOsHttpProxy = false;

    public function withSystemHttpProxy(): static
    {
        $this->enableOsHttpProxy = true;
        return $this;
    }

    public bool $enableCompiledCached = false;

    public function withCompiledCached(): static
    {
        $this->enableCompiledCached = true;
        return $this;
    }
}

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

    public bool $enableBuildLibraryCached = true;

    public string $preInstallCommand = '';

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

    public function withCleanBuildDirectory(): static
    {
        if (PHP_CLI_BUILD_TYPE == 'dev') {
            $this->cleanBuildDirectory = true;
        }
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

    public function withBuildLibraryCached(bool $enableBuildLibraryCached): static
    {
        $this->enableBuildLibraryCached = $enableBuildLibraryCached;
        return $this;
    }

    public function withPreInstallCommand(string $preInstallCommand): static
    {
        $this->preInstallCommand = $preInstallCommand;
        return $this;
    }
}

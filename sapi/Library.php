<?php

namespace SwooleCli;

use SwooleCli\Project;

class Library extends Project
{
    public string $url;

    public array $mirrorUrls = [];

    public string $configure = '';

    public string $file = '';

    public string $ldflags = '';

    public bool $cleanBuildDirectory = false;

    public bool $cleanPreInstallDirectory = false;
    public string $preInstallDirectory = '';

    public string $buildScript = '';

    public string $makeOptions = '';
    public string $makeVariables = '';

    public string $makeInstallCommand = 'install';
    public string $makeInstallOptions = '';
    public string $beforeInstallScript = '';
    public string $afterInstallScript = '';
    public string $pkgConfig = '';
    public string $pkgName = '';


    public string $prefix = '/usr';
    public bool $skipBuildLicense = false;
    public bool $skipDownload = false;
    public bool $skipBuildInstall = false;

    public string $untarArchiveCommand = 'tar';
    public string $beforeConfigureScript = '';
    public string $binPath = '';

    public string $label = '';

    public function withUrl(string $url): static
    {
        $this->url = $url;
        return $this;
    }
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

    public function withCleanBuildDirectory(): static
    {
        if (!SWOOLE_CLI_BUILD_TYPE) {
            $this->cleanBuildDirectory = true;
        }
        return $this;
    }

    public function withCleanPreInstallDirectory(string $pre_install_dir): static
    {
        if (!empty($this->prefix) && ($this->prefix != '/usr') &&  !empty($pre_install_dir)) {
            if (!SWOOLE_CLI_BUILD_TYPE) {
                $this->cleanPreInstallDirectory = true;
                $this->preInstallDirectory = $pre_install_dir;
            }
        }
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
        $this->pkgName = $pkgName;
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

    public function withUntarArchiveCommand(string $command): static
    {
        $this->untarArchiveCommand = $command;
        return $this;
    }

    public function withScriptBeforeConfigure(string $script): static
    {
        $this->beforeConfigureScript = $script;
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
        return $this->skipDownload ;
    }

    public function disableDefaultLdflags(): static
    {
        $this->ldflags = '';
        return $this;
    }

    public function withBinPath(string $path): static
    {
        $this->binPath = $path;
        return $this;
    }

    public function disableDefaultPkgConfig(): static
    {
        $this->pkgConfig = '';
        return $this;
    }

    public function disablePkgName(): static
    {
        $this->pkgName = '';
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
}

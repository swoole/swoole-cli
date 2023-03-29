<?php

namespace SwooleCli;

class Extension extends Project
{
    public string $options = '';
    public string $peclVersion = '';

    public function withOptions(string $options): static
    {
        $this->options = $options;
        return $this;
    }

    public function withPeclVersion(string $peclVersion): static
    {
        $this->peclVersion = $peclVersion;
        return $this;
    }
}

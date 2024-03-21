<?php

namespace SwooleCli\PreprocessorTrait;

trait CompilerTrait
{
    public function set_C_COMPILER(string $cc): static
    {
        $this->cCompiler = $cc;
        return $this;
    }

    public function set_CXX_COMPILER(string $cxx): static
    {
        $this->cppCompiler = $cxx;
        return $this;
    }

    public function get_C_COMPILER(): string
    {
        return $this->cCompiler;
    }

    public function get_CXX_COMPILER(): string
    {
        return $this->cppCompiler;
    }
}


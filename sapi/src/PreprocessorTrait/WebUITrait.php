<?php

declare(strict_types=1);

namespace SwooleCli\PreprocessorTrait;

use function sort;

trait WebUITrait
{
    protected function generateWebUIData(): void
    {
        $this->mkdirIfNotExists($this->rootDir . '/var/webui/', 0777, true);
        $data = $this->extEnabled;
        sort($data);
        file_put_contents(
            $this->rootDir . '/var/webui/default_extension_list.json',
            json_encode($data)
        );
        $directory = $this->rootDir . '/sapi/src/builder/extension';
        file_put_contents(
            $this->rootDir . '/var/webui/extension_list.json',
            json_encode(
                array_values(
                    array_map(
                        fn ($x) => str_replace('.php', '', $x),
                        array_diff(scandir($directory), array('..', '.'))
                    )
                )
            )
        );
    }
}

<?php declare(strict_types=1);

namespace Pull\Domain;

interface ConfigLoader
{
    public function loadFile(string $path): Config;

    public function load(string $data): Config;

    public function defaultConfigPath(): string;
}

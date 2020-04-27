<?php declare(strict_types=1);

namespace Pull\Infrastructure\Config;

use Nette\Neon\Neon;
use Pull\Domain\Config;
use Pull\Domain\ConfigLoader;

use function file_get_contents;
use function getenv;
use function is_readable;
use function sprintf;

class NeonConfigLoader implements ConfigLoader
{
    public function loadFile(string $path): Config
    {
        if (!is_readable($path)) {
            return Config::fromArray([]);
        }

        $fileContents = file_get_contents($path);
        if ($fileContents === false) {
            return Config::fromArray([]);
        }

        return $this->load($fileContents);
    }

    public function load(string $data): Config
    {
        $decoded = Neon::decode($data);

        return Config::fromArray($decoded);
    }

    public function defaultConfigPath(): string
    {
        return sprintf('%s/.config/pull/config.neon', getenv('HOME'));
    }
}

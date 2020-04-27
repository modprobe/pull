<?php declare(strict_types=1);

namespace Pull\Domain;

class Config
{
    private ?string $projectDir;
    private int $maxDepth = 5;

    /**
     * @param array<string, string|int> $configData
     * @noinspection SuspiciousBinaryOperationInspection
     */
    public static function fromArray(array $configData): self
    {
        $config = new self();

        $config->projectDir = static::expandTilde((string) $configData['projectDir'] ?? null);
        $config->maxDepth   = (int) $configData['maxDepth'] ?? 5;

        return $config;
    }

    protected function __construct()
    {
    }

    public function projectDir(): ?string
    {
        return $this->projectDir;
    }

    public function maxDepth(): int
    {
        return $this->maxDepth;
    }

    private static function expandTilde(?string $path): ?string
    {
        if ($path === null) {
            return null;
        }

        if (function_exists('posix_getuid') && strpos($path, '~') !== false) {
            $info = posix_getpwuid(posix_getuid());
            $path = str_replace('~', $info['dir'], $path);
        }

        return $path;
    }
}

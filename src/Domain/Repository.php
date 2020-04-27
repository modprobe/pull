<?php declare(strict_types=1);

namespace Pull\Domain;

use Symfony\Component\Process\Process;
use Webmozart\Assert\Assert;

use function trim;

class Repository
{
    private string $path;

    public static function create(string $path): self
    {
        Assert::directory($path);

        return new self($path);
    }

    private function __construct(string $path)
    {
        $this->path = $path;
    }

    public function path(): string
    {
        return $this->path;
    }


}

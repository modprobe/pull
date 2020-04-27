<?php declare(strict_types=1);

namespace Pull\Domain;

use Iterator;
use IteratorIterator;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Webmozart\Assert\Assert;

use function dirname;
use function realpath;
use function sprintf;
use function strpos;

abstract class RepositoryFinder extends IteratorIterator
{
    public static function forRootDirectory(string $path, int $maxDepth = 5): self
    {
        Assert::directory($path);

        return new static(static::defaultFinder($path, $maxDepth));  // @phpstan-ignore-line
    }

    public function __construct(Finder $finder)
    {
        $finder = $this->setUpFinder($finder);

        parent::__construct($finder);
    }

    /**
     * Convert iterator values into Repository domain objects.
     * @see Iterator::current()
     */
    public function current(): Repository
    {
        /** @var SplFileInfo $fileInfo */
        $fileInfo = parent::current();

        /** @var string $path */
        $path = $fileInfo->getRealPath();
        $path = dirname($path);

        return Repository::create($path);
    }

    private static function defaultFinder(string $path, int $maxDepth): Finder
    {
        return Finder::create()->in($path)->depth(sprintf('< %d', $maxDepth));
    }

    protected function setUpFinder(Finder $finder): Finder
    {
        // @phpstan-ignore-next-line
        return $finder
            ->ignoreDotFiles(false)
            ->ignoreVCS(false)
            ->directories()
            ->filter(static::filterVendorFolders());
    }

    private static function filterVendorFolders(): callable
    {
        return static function (SplFileInfo $fileInfo) {
            return strpos($fileInfo->getRealPath() ?: '', '/vendor/') === false;
        };
    }
}

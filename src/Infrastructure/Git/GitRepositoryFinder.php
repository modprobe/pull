<?php declare(strict_types=1);

namespace Pull\Infrastructure\Git;

use Pull\Domain\RepositoryFinder;
use Symfony\Component\Finder\Finder;

class GitRepositoryFinder extends RepositoryFinder
{
    protected function setUpFinder(Finder $finder): Finder
    {
        return parent::setUpFinder($finder)
            ->name('.git');
    }
}

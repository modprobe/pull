<?php declare(strict_types=1);

use Pull\Domain\ConfigLoader;
use Pull\Domain\RepositoryInfo;
use Pull\Domain\RepositoryUpdater;
use Pull\Infrastructure\Config\NeonConfigLoader;
use Pull\Infrastructure\Git\GitRepositoryUtils;
use function DI\create;

return [
    RepositoryUpdater::class => create(GitRepositoryUtils::class),
    RepositoryInfo::class => create(GitRepositoryUtils::class),
    ConfigLoader::class => create(NeonConfigLoader::class),
];

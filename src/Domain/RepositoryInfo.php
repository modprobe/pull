<?php declare(strict_types=1);

namespace Pull\Domain;

interface RepositoryInfo
{
    public function hash(Repository $repository, ?string $revName): string;
}

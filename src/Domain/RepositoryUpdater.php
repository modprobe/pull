<?php declare(strict_types=1);

namespace Pull\Domain;

interface RepositoryUpdater
{
    public function update(Repository $repository): UpdateResult;
}

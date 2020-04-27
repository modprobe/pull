<?php declare(strict_types=1);

namespace Pull\Infrastructure\Git;

use Pull\Domain\Repository;
use Pull\Domain\RepositoryInfo;
use Pull\Domain\RepositoryUpdater;
use Pull\Domain\UpdateResult;
use Pull\Domain\UpdateStatus;
use Symfony\Component\Process\Process;
use function trim;

class GitRepositoryUtils implements RepositoryUpdater, RepositoryInfo
{
    public function update(Repository $repository): UpdateResult
    {
        $process  = new Process(['git', 'pull'], $repository->path());
        $exitCode = $process->run();

        return $exitCode === 0
            ? new UpdateResult($repository, UpdateStatus::SUCCESS(), $process->getOutput())
            : new UpdateResult($repository, UpdateStatus::FAILED(), $process->getErrorOutput())
        ;
    }

    public function hash(Repository $repository, ?string $revision): string
    {
        $revision = $revision ?? 'HEAD';

        $process = new Process(['git', 'rev-parse', '--quiet', '--verify', $revision], $repository->path());
        $process->run();

        return trim($process->getOutput());
    }
}

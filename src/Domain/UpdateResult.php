<?php declare(strict_types=1);

namespace Pull\Domain;

class UpdateResult
{
    private Repository $repo;
    private UpdateStatus $status;
    private ?string $verboseOutput;

    public function __construct(Repository $repo, UpdateStatus $status, ?string $verboseOutput)
    {
        $this->repo          = $repo;
        $this->status        = $status;
        $this->verboseOutput = $verboseOutput;
    }

    public function repo(): Repository
    {
        return $this->repo;
    }

    public function status(): UpdateStatus
    {
        return $this->status;
    }

    public function verboseOutput(): ?string
    {
        return $this->verboseOutput;
    }
}

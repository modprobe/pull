<?php declare(strict_types=1);

namespace Pull;

use Pull\Domain\ConfigLoader;
use Pull\Domain\Repository;
use Pull\Domain\RepositoryUpdater;
use Pull\Domain\UpdateResult;
use Pull\Domain\UpdateStatus;
use Pull\Infrastructure\Git\GitRepositoryFinder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use function count;
use function sprintf;

class PullCommand extends Command
{
    protected static $defaultName = 'pull';

    private RepositoryUpdater $updater;
    private ConfigLoader $configLoader;
    private OutputInterface $output;

    public function __construct(RepositoryUpdater $updater, ConfigLoader $configLoader)
    {
        $this->updater = $updater;
        $this->configLoader = $configLoader;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'path',
                InputArgument::OPTIONAL,
                'The path to use for discovering and updating repositories. Only required if not set in config.'
            )
            ->addOption(
                'config',
                'c',
                InputOption::VALUE_REQUIRED,
                'Path to config file',
                $this->configLoader->defaultConfigPath()
            )
            ->addOption(
                'maxDepth',
                'm',
                InputOption::VALUE_REQUIRED,
                'Maximum recursive depth',
                5
            )
        ;
    }

    /** @param ConsoleOutput $output */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;

        /** @var string $configPath */
        $configPath = $input->getOption('config');
        $config     = $this->configLoader->load($configPath);

        if ($input->getArgument('path') === null && $config->projectDir() === null) {
            $this->io($output)->error('Path is neither set in a config file nor provided as an argument.');
            return 1;
        }

        /** @var string $projectDir */
        $projectDir = $input->getArgument('path') ?? $config->projectDir();

        if ($output->isVerbose()) {
            $this->io($output)->comment(sprintf('projectDir = %s', $projectDir));
        }

        /**
         * @noinspection SuspiciousBinaryOperationInspection
         * @phpstan-ignore-next-line
         */
        $maxDepth = (int) $input->getOption('maxDepth') ?? $config->maxDepth();

        $finder = GitRepositoryFinder::forRootDirectory($projectDir, $maxDepth);
        $failed = [];

        foreach ($finder as $gitRepository) {
            $progressSection = $output->section();
            $this->outputStatus($progressSection, $gitRepository);

            $result = $this->updater->update($gitRepository);

            $this->outputStatus($progressSection, $gitRepository, $result);

            if ($result->status()->equals(UpdateStatus::FAILED())) {
                $failed[] = $result;
            }
        }

        if (count($failed) > 0) {
            $this->outputFailed($failed);
        }

        return 0;
    }

    private function outputStatus(ConsoleSectionOutput $section, Repository $repo, ?UpdateResult $result = null): void
    {

        switch (true) {
            case $result === null:
                $section->overwrite('⏳ Updating ' . $repo->path());
                break;
            case $result->status()->equals(UpdateStatus::SUCCESS()):
                $section->overwrite('✅ Updated ' . $repo->path());
                break;
            case $result->status()->equals(UpdateStatus::FAILED()):
                $section->overwrite('🚩 Failed to update ' . $repo->path());
                break;
        }

        if ($result !== null && $this->output->isVerbose()) {
            $this->io($section)->comment($result->verboseOutput() ?? '');
        }
    }

    /** @param UpdateResult[] $failed */
    private function outputFailed(array $failed): void
    {
        $this->output->writeln('');

        $table = [];
        foreach ($failed as $result) {
            $table[] = [$result->repo()->path(), $result->verboseOutput()];
        }

        $this->io($this->output)->table(['Repo', 'Output'], $table);
    }

    private function io(OutputInterface $output): SymfonyStyle
    {
        return new SymfonyStyle(new ArrayInput([]), $output);
    }
}

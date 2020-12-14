<?php

declare(strict_types=1);

namespace App\Command;

use App\Services\Git\GitService;
use App\Services\Hook\HookConfigurationService;
use App\Services\Hook\HookExecutionService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PostMergeCommand extends Command
{
    protected static $defaultName = 'post-merge';
    private HookConfigurationService $hookConfigurationService;
    private HookExecutionService $hookExecutionService;

    public function __construct()
    {
        parent::__construct();
        $this->hookConfigurationService = new HookConfigurationService();
        $this->hookExecutionService = new HookExecutionService();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('is-squash-commit');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $previousHead = 'ORIG_HEAD';
        $newHead = 'HEAD';

        $changedFiles = GitService::getChangedFiles($previousHead, $newHead);
        $hooks = $this->hookConfigurationService->getExecutableHookConfigModels($changedFiles);
        foreach ($hooks as $hook) {
            $this->hookExecutionService->executeHook($hook, $symfonyStyle);
        }

        return 0;
    }
}

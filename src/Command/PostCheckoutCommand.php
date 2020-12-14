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

class PostCheckoutCommand extends Command
{
    private const CHECKOUT_TYPE_BRANCH = 'branch';
    private const CHECKOUT_TYPE_FILE = 'file';
    protected static $defaultName = 'post-checkout';
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
            ->addArgument('previous-head')
            ->addArgument('new-head')
            ->addArgument('checkout-type')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $previousHead = $input->getArgument('previous-head');
        $newHead = $input->getArgument('new-head');
        $checkoutType = $input->getArgument('checkout-type') === "1" ? self::CHECKOUT_TYPE_BRANCH : self::CHECKOUT_TYPE_FILE;

        if ($checkoutType === self::CHECKOUT_TYPE_BRANCH) {
            $changedFiles = GitService::getChangedFiles($previousHead, $newHead);
            $hooks = $this->hookConfigurationService->getExecutableHookConfigModels($changedFiles);
            foreach ($hooks as $hook) {
                $this->hookExecutionService->executeHook($hook, $symfonyStyle);
            }
        }

        return 0;
    }
}

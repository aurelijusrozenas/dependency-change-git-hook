<?php

declare(strict_types=1);

namespace App\Command;

use App\Services\Hook\HookConfigurationService;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

use function basename;
use function dirname;
use function getcwd;
use function is_dir;
use function sprintf;

class InstallCommand extends Command
{
    protected static $defaultName = 'install';
    private HookConfigurationService $hookConfigurationService;

    public function __construct(HookConfigurationService $hookConfigurationService)
    {
        parent::__construct();
        $this->hookConfigurationService = $hookConfigurationService;
    }

    protected function configure(): void
    {
        $this->addOption('no-interaction', 'n', InputOption::VALUE_NONE, 'Skip all questions and answer them "yes".');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $filesystem = new Filesystem();
        $currentCliDir = getcwd();
        $hookDir = sprintf('%s/.git/hooks', $currentCliDir);
        if (!$filesystem->exists($hookDir) || !is_dir($hookDir)) {
            throw new RuntimeException(sprintf('Directory "%s" must exist.', $hookDir));
        }

        /* create symlinks to post-checkout and post-merge */
        $symfonyStyle->title('Creating symlink for post-checkout hook.');
        $projectPath = dirname(__DIR__, 2);
        $projectDirName = basename($projectPath);
        $gitHooksToLink = ['post-checkout', 'post-merge'];
        foreach ($gitHooksToLink as $gitHookToLink) {
            $gitHookSymlinkSource = sprintf('%s/%s', $hookDir, $gitHookToLink);
            $gitHookSymlinkTarget = sprintf('./%s/bin/%s', $projectDirName, $gitHookToLink);
            if (!$filesystem->exists($gitHookSymlinkSource)) {
                $filesystem->symlink($gitHookSymlinkTarget, $gitHookSymlinkSource);
                $symfonyStyle->success('Created.');
            } elseif ($filesystem->readlink($gitHookSymlinkSource) === $gitHookSymlinkTarget) {
                $symfonyStyle->success('Already exists.');
            } else {
                $symfonyStyle->warning(sprintf('File "%s" already exists, skipping.', $gitHookSymlinkSource));
            }
        }

        /* register scripts */
        $symfonyStyle->title('Registering scripts.');
        $symfonyStyle->comment('Each script will run after branch change or pull when changed files matched predefined patterns.');

        $hookConfigModels = $this->hookConfigurationService->getHookConfigModels();

        foreach ($hookConfigModels as $hookConfigModel) {
            $hookFilePath = sprintf('%s/%s', $hookDir, $hookConfigModel->getFilename());
            $hookTemplateFilePath = sprintf('%s/%s', __DIR__.'/../../hook-templates', $hookConfigModel->getFilename());
            $hookFileExists = $filesystem->exists($hookFilePath);

            $symfonyStyle->block($hookConfigModel->getTitle());
            $symfonyStyle->writeln(sprintf('Script "<comment>%s</comment>" will be run when change is detected in files:', $hookFilePath));
            $symfonyStyle->listing($hookConfigModel->getFilesToWatch());
            $isConfirmed = $symfonyStyle->askQuestion(
                new ConfirmationQuestion(sprintf('Do you want to enable script for "%s"?', $hookConfigModel->getTitle()))
            );
            if ($isConfirmed) {
                if (!$hookFileExists) {
                    $isCopyConfirmed = $symfonyStyle->askQuestion(
                        new ConfirmationQuestion(
                            sprintf('Do you want to copy example there? Otherwise you have to create the file your self and make it executable.')
                        )
                    );
                    if ($isCopyConfirmed) {
                        $symfonyStyle->writeln('Copying template...');
                        $filesystem->copy($hookTemplateFilePath, $hookFilePath);
                        $symfonyStyle->writeln('Making executable...');
                        $filesystem->chmod($hookFilePath, 0755);
                        $symfonyStyle->success('Done. Adjust this file for your needs.');
                    }
                } else {
                    $symfonyStyle->success('Script already exists. Adjust this file for your needs.');
                }
            } elseif ($hookFileExists) {
                $isRemoveConfirmed = $symfonyStyle->askQuestion(
                    new ConfirmationQuestion(
                        sprintf(
                            sprintf(
                                'File "%s" exists and will still be run after code update. <comment>Do you want to remove it?</comment>'.
                                ' This is irreversible, file will be lost.',
                                $hookFilePath
                            )
                        ), false
                    )
                );
                if ($isRemoveConfirmed) {
                    $symfonyStyle->writeln('Removing...');
                    $filesystem->remove($hookFilePath);
                    $symfonyStyle->success('Removed.');
                } else {
                    $symfonyStyle->warning('Script was left intact and will still be run after code update.');
                }
            }
        }

        return 0;
    }
}

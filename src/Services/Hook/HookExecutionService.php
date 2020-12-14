<?php

declare(strict_types=1);

namespace App\Services\Hook;

use App\Model\HookConfigModel;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

use function getcwd;
use function is_executable;
use function sprintf;

class HookExecutionService
{
    /** @noinspection PhpUnusedParameterInspection */
    public function executeHook(HookConfigModel $hookConfigModel, SymfonyStyle $symfonyStyle): void
    {
        $currentDir = getcwd();

        $fullPath = sprintf('%s/.git/hooks/%s', $currentDir, $hookConfigModel->getFilename());

        $symfonyStyle->getFormatter()->setStyle('script-path', new OutputFormatterStyle('yellow', null));

        $filesystem = new Filesystem();
        if (!$filesystem->exists($fullPath)) {
            $symfonyStyle->warning(sprintf('Create file "%s" to execute when changes for hook "%s" are detected.', $fullPath, $hookConfigModel->getTitle()));
        } elseif (!is_executable($fullPath)) {
            $symfonyStyle->error(sprintf('Make "%s" executable.', $fullPath));
        } else {
            $symfonyStyle->writeln(sprintf('<script-path>Running "%s"</script-path>', $fullPath));
            $process = Process::fromShellCommandline($fullPath);
            $process->mustRun(
                function ($type, $buffer) {
                    echo $buffer;
                }
            );
        }
    }
}

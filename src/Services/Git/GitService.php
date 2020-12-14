<?php

declare(strict_types=1);

namespace App\Services\Git;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

use function explode;
use function trim;

class GitService
{
    public static function getChangedFiles(string $previousHead, string $newHead): array
    {
        $command = "git diff-tree -r --name-only --no-commit-id {$previousHead} {$newHead}";
        $process = new Process(explode(' ', $command));
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $changedFiles = explode("\n", trim($process->getOutput()));

        return $changedFiles;
    }
}

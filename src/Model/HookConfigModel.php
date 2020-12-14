<?php

declare(strict_types=1);

namespace App\Model;

class HookConfigModel
{
    private string $title;
    private string $filename;
    /** @var string[] */
    private array $filesToWatch;

    public function __construct(string $title, string $filename, array $filesToWatch)
    {
        $this->title = $title;
        $this->filename = $filename;
        $this->filesToWatch = $filesToWatch;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getFilesToWatch(): array
    {
        return $this->filesToWatch;
    }
}

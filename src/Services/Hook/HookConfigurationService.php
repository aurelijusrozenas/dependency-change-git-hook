<?php

declare(strict_types=1);

namespace App\Services\Hook;

use App\Model\HookConfigModel;

use Symfony\Component\Finder\Finder;

use function array_filter;
use function array_intersect;
use function count;

class HookConfigurationService
{
    /**
     * @var HookConfigModel[]
     */
    private array $hookConfigModels;

    /**
     * @param HookConfigModel[] $hookConfigModels
     */
    public function __construct()
    {
        $finder = new Finder();
        $finder->files()->in(__DIR__.'/../../../hook-config');
        $hookConfigModels = [];
        foreach ($finder as $file) {
            /** @var HookConfigModel $hookConfigModel */
            /** @noinspection PhpIncludeInspection */
            $hookConfigModels[] = require $file->getRealPath();
        }
        $this->hookConfigModels = $hookConfigModels;
    }

    /**
     * @return HookConfigModel[]
     */
    public function getHookConfigModels(): array
    {
        return $this->hookConfigModels;
    }

    /**
     * @param string[] $changedFiles
     *
     * @return HookConfigModel[]
     */
    public function getExecutableHookConfigModels(array $changedFiles): array
    {
        return array_filter(
            $this->hookConfigModels,
            static function (HookConfigModel $hookConfigModel) use ($changedFiles): bool {
                return count(array_intersect($hookConfigModel->getFilesToWatch(), $changedFiles)) > 0;
            }
        );
    }
}

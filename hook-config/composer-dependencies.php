<?php

declare(strict_types=1);

use App\Model\HookConfigModel;

return new HookConfigModel(
    'Composer dependencies',
    'post-composer-dependencies-update.sh',
    [
        'composer.json',
        'composer.lock',
    ],
);

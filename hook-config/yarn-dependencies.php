<?php

declare(strict_types=1);

use App\Model\HookConfigModel;

return new HookConfigModel(
    'Yarn dependencies',
    'post-yarn-dependencies-update.sh',
    [
        'package.json',
        'yarn.lock',
    ],
);

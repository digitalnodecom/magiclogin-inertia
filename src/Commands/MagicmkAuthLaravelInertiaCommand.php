<?php

namespace DigitalNode\MagicmkAuthLaravelInertia\Commands;

use Illuminate\Console\Command;

class MagicmkAuthLaravelInertiaCommand extends Command
{
    public $signature = 'magicmk-auth-laravel-inertia';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}

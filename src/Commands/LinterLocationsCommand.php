<?php

namespace Pardalsalcap\LinterLocations\Commands;

use Illuminate\Console\Command;

class LinterLocationsCommand extends Command
{
    public $signature = 'linter-locations';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}

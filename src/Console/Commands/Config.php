<?php

namespace Biigle\Modules\Demo\Console\Commands;

use Biigle\Modules\Demo\DemoServiceProvider as ServiceProvider;
use Illuminate\Console\Command;

class Config extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'demo:config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish the configuration of this package';

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $this->call('vendor:publish', [
            '--provider' => ServiceProvider::class,
            '--tag' => ['config'],
        ]);
    }
}

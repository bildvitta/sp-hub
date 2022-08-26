<?php

namespace BildVitta\SpHub\Console\Commands\DataImport\Hub;

use BildVitta\SpHub\Console\Commands\DataImport\Hub\Jobs\HubImportJob;
use Illuminate\Console\Command;

class HubImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dataimport:hub {--select=500}';

    /**
     * @var int
     */
    private int $selectLimit = 500;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call init sync users in database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting import');
        
        if (! class_exists('\App\Models\Worker')) {
            $this->info('Error: class \App\Models\Worker not exists');
            return 1;
        }

        if ($selectLimit = $this->option('select')) {
            $this->selectLimit = (int) $selectLimit;
        }

        $worker = new \App\Models\Worker();
        $worker->type = 'sp-hub.dataimport';
        $worker->status = 'created';
        $worker->schedule = now();
        $worker->payload = [
            'limit' => $this->selectLimit,
            'offset' => 0,
            'total' => null,
            'current_table' => 'companies',
            'finished_tables' => [],
            'tables' => ['companies', 'users'],
        ];
        $worker->save();

        HubImportJob::dispatch($worker->id);

        $this->info('Worker type: sp-hub.dataimport');
        $this->info('Job started, command execution ended');
 
        return 0;
    }
}

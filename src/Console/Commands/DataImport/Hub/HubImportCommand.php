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
    protected $signature = 'dataimport:hub {--select=500} {--offset=0} {--table=0}';

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

        $selectLimit = 500;
        if ($optionSelect = $this->option('select')) {
            $selectLimit = (int) $optionSelect;
        }

        $offset = 0;
        if ($optionOffset = $this->option('offset')) {
            $offset = (int) $optionOffset;
        }
        
        $tableIndex = 0;
        if ($optionTableIndex = $this->option('table')) {
            $tableIndex = (int) $optionTableIndex;
        }

        $worker = new \App\Models\Worker();
        $worker->type = 'sp-hub.dataimport';
        $worker->status = 'created';
        $worker->schedule = now();
        $worker->payload = [
            'limit' => $selectLimit,
            'offset' => $offset,
            'total' => null,
            'table_index' => $tableIndex,
            'tables' => [
                0 => 'companies',
                1 => 'users',
            ],
        ];
        $worker->save();

        HubImportJob::dispatch($worker->id);

        $this->info('Worker type: sp-hub.dataimport');
        $this->info('Job started, command execution ended');
 
        return 0;
    }
}

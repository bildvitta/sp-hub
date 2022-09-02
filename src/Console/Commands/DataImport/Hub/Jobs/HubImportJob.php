<?php

namespace BildVitta\SpHub\Console\Commands\DataImport\Hub\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use BildVitta\SpHub\Models\Worker;
use BildVitta\SpHub\Console\Commands\DataImport\Hub\Resources\CompanyImport;
use BildVitta\SpHub\Console\Commands\DataImport\Hub\Resources\ConfigConnection;
use BildVitta\SpHub\Console\Commands\DataImport\Hub\Resources\DbHubCompany;
use BildVitta\SpHub\Console\Commands\DataImport\Hub\Resources\DbHubUser;
use BildVitta\SpHub\Console\Commands\DataImport\Hub\Resources\UserImport;
use InvalidArgumentException;
use Throwable;

class HubImportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use ConfigConnection;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 3300;

    /**
     * @var int
     */
    public $retryAfter = 60;

    /**
     * @var int
     */
    private int $workerId;

    /**
     * @var string
     */
    private string $currentTable;

    /**
     * @var Worker
     */
    private $worker;

    /**
     * @param int $workerId
     */
    public function __construct(int $workerId)
    {
        $this->onQueue('default');
        $this->workerId = $workerId;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws InvalidArgumentException
     */
    public function handle()
    {
        if (! $this->worker = Worker::find($this->workerId)) {
            return;
        }
        $this->init();

        switch ($this->currentTable) {
            case 'companies':
                $this->importCompanies();
                break;
            case 'users':
                $this->importUsers();
                break;
            default:
                throw new InvalidArgumentException('Invalid current table');
        }
    }

    /**
     * @return void
     */
    private function init(): void
    {
        $this->configConnection();
        $this->updateWorker(['status' => 'in_progress']);
        $this->currentTable = $this->worker->payload->tables[$this->worker->payload->table_index];
    }

    /**
     * @return void
     */
    private function importCompanies(): void
    {
        $dbHubCompany = app(DbHubCompany::class);
        $companyImport = app(CompanyImport::class);
        $payload = $this->worker->payload;

        if (is_null($payload->total)) {
            $payload->total = $dbHubCompany->totalRecords();
            $this->updateWorker(['payload' => $payload]);
        }

        $companies = collect($dbHubCompany->getCompanies($payload->limit, $payload->offset));
        foreach ($companies as $company) {
            $companyImport->import($company);
        }
        
        $this->dispatchNextJob();
    }

    /**
     * @return void
     */
    private function importUsers(): void
    {
        $dbHubUser = app(DbHubUser::class);
        $userImport = app(UserImport::class);
        $payload = $this->worker->payload;

        if (is_null($payload->total)) {
            $payload->total = $dbHubUser->totalRecords();
            $this->updateWorker(['payload' => $payload]);
        }

        $users = collect($dbHubUser->getUsers($payload->limit, $payload->offset));
        foreach ($users as $user) {
            $userImport->import($user);
        }

        $this->dispatchNextJob();
    }

    /**
     * @return void
     */
    private function dispatchNextJob(): void
    {
        if (! $this->worker = Worker::find($this->workerId)) {
            return;
        }
        $payload = $this->worker->payload;
        $nextOffset = $payload->offset + $payload->limit;
        
        if ($nextOffset < $payload->total) {
            $payload->offset = $nextOffset;
            $this->updateWorker(['payload' => $payload]);
            HubImportJob::dispatch($this->worker->id);
        } else {
            $nextTableIndex = $payload->table_index + 1;
            if (isset($payload->tables[$nextTableIndex])) {
                $payload->table_index = $nextTableIndex;
                $payload->offset = 0;
                $payload->total = null;
                $this->updateWorker(['payload' => $payload]);
                HubImportJob::dispatch($this->worker->id);
            } else {
                unset($payload->table_index);
                unset($payload->offset);
                unset($payload->total);
                $payload->finished_jobs = true;
                $this->updateWorker(['payload' => $payload, 'status' => 'finished']);
            }
        }
    }

    /**
     * @param array $props
     * @return void
     */
    private function updateWorker(array $props): void
    {
        foreach ($props as $key => $value) {
            $this->worker->{$key} = $value;
        }
        $this->worker->created_at = now();
        $this->worker->save();
    }

    /**
     * @param Throwable $exception
     * @return void
     */
    public function failed(Throwable $exception): void
    {
        if (! $worker = Worker::find($this->workerId)) {
            return;
        }
        $worker->error = [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ];
        $worker->status = 'error';
        $worker->save();
    }
}

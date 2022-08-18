<?php

namespace BildVitta\SpHub\Console\Commands\DataImport\Hub;

use BildVitta\SpHub\Console\Commands\DataImport\Hub\Resources\CompanyImport;
use BildVitta\SpHub\Console\Commands\DataImport\Hub\Resources\DbHubCompany;
use BildVitta\SpHub\Console\Commands\DataImport\Hub\Resources\DbHubUser;
use BildVitta\SpHub\Console\Commands\DataImport\Hub\Resources\UserImport;
use Illuminate\Console\Command;

class HubImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dataimport:hub';

    /**
     * @var DbHubUser
     */
    private DbHubUser $dbHubUser;

    /**
     * @var DbHubCompany
     */
    private DbHubCompany $dbHubCompany;

    /**
     * @var UserImport
     */
    private UserImport $userImport;

    /**
     * @var CompanyImport
     */
    private CompanyImport $companyImport;

    /**
     * @var int
     */
    private int $selectLimit = 200;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call init sync users in database';

    /**
     * @param DbHubUser $dbHubUser
     * @param UserImport $userImport
     */
    public function __construct(
        DbHubUser $dbHubUser,
        DbHubCompany $dbHubCompany,
        UserImport $userImport,
        CompanyImport $companyImport,
    ) {
        parent::__construct();
        $this->dbHubUser = $dbHubUser;
        $this->dbHubCompany = $dbHubCompany;
        $this->userImport = $userImport;
        $this->companyImport = $companyImport;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->configConnection();

        $this->importCompanies();

        $this->importUsers();

        return 0;
    }

    /**
     * @return void
     */
    private function importCompanies(): void
    {
        $this->newLine();
        $this->info('Starting import companies');
        $totalRecords = $this->dbHubCompany->totalRecords();
        $this->newLine();
        $bar = $this->output->createProgressBar($totalRecords);
        $bar->start();

        $loop = ceil($totalRecords / $this->selectLimit);
        for ($i = 0; $i < $loop; $i++) {
            $offset = $this->selectLimit * $i;
            $companies = collect($this->dbHubCompany->getCompanies($this->selectLimit, $offset));
            foreach ($companies as $company) {
                $this->companyImport->import($company);
                $bar->advance(1);
            }
        }
        $bar->finish();

        $this->newLine(2);
        $this->info('Import companies finished');
        $this->newLine();
    }

    /**
     * @return void
     */
    private function importUsers(): void
    {
        $this->newLine();
        $this->info('Starting import users');
        $totalRecords = $this->dbHubUser->totalRecords();
        $this->newLine();
        $bar = $this->output->createProgressBar($totalRecords);
        $bar->start();

        $loop = ceil($totalRecords / $this->selectLimit);
        for ($i = 0; $i < $loop; $i++) {
            $offset = $this->selectLimit * $i;
            $users = collect($this->dbHubUser->getUsers($this->selectLimit, $offset));
            foreach ($users as $user) {
                $this->userImport->import($user);
                $bar->advance(1);
            }
        }
        $bar->finish();

        $this->newLine(2);
        $this->info('Import users finished');
        $this->newLine();
    }

    /**
     * @return void
     */
    private function configConnection(): void
    {
        config([
            'database.connections.sp_hub' => [
                'driver' => 'mysql',
                'host' => config('sp-hub.db.host'),
                'port' => config('sp-hub.db.port'),
                'database' => config('sp-hub.db.database'),
                'username' => config('sp-hub.db.username'),
                'password' => config('sp-hub.db.password'),
                'unix_socket' => env('DB_SOCKET', ''),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => true,
                'engine' => null,
                'options' => [],
            ]
        ]);
    }
}

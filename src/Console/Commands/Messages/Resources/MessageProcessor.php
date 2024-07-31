<?php

namespace BildVitta\SpHub\Console\Commands\Messages\Resources;

use BildVitta\SpHub\Console\Commands\Messages\Resources\Helpers\BrandsHelper;
use BildVitta\SpHub\Console\Commands\Messages\Resources\Helpers\CompanyHelper;
use BildVitta\SpHub\Console\Commands\Messages\Resources\Helpers\CompanyLinksHelper;
use BildVitta\SpHub\Console\Commands\Messages\Resources\Helpers\LogHelper;
use BildVitta\SpHub\Console\Commands\Messages\Resources\Helpers\PermissionHelper;
use BildVitta\SpHub\Console\Commands\Messages\Resources\Helpers\PositionsHelper;
use BildVitta\SpHub\Console\Commands\Messages\Resources\Helpers\RoleHelper;
use BildVitta\SpHub\Console\Commands\Messages\Resources\Helpers\UserHelper;
use PhpAmqpLib\Message\AMQPMessage;
use stdClass;
use Throwable;

class MessageProcessor
{
    use BrandsHelper;
    use CompanyHelper;
    use CompanyLinksHelper;
    use LogHelper;
    use PermissionHelper;
    use PositionsHelper;
    use RoleHelper;
    use UserHelper;

    /**
     * @var string
     */
    public const USERS = 'users';

    /**
     * @var string
     */
    public const COMPANIES = 'companies';

    /**
     * @var string
     */
    public const PERMISSIONS = 'permissions';

    /**
     * @var string
     */
    public const ROLES = 'roles';

    /**
     * @var string
     */
    public const POSITIONS = 'positions';

    /**
     * @var string
     */
    public const USER_COMPANIES = 'user_companies';

    public const BRANDS = 'brands';

    /**
     * @var string
     */
    public const CREATED = 'created';

    /**
     * @var string
     */
    public const UPDATED = 'updated';

    /**
     * @var string
     */
    public const DELETED = 'deleted';

    /**
     * @var string
     */
    public const SUPERVISOR_BROKERS_UPDATED = 'supervisor_brokers_updated';

    public function process(AMQPMessage $message): void
    {
        $message->ack();
        $messageBody = null;
        $messageData = null;
        try {
            $properties = $message->get_properties();
            $messageBody = $message->getBody();
            $messageData = json_decode($messageBody);
            $properties = explode('.', $properties['type']);
            $type = $properties[0];
            $operation = $properties[1];

            switch ($type) {
                case self::BRANDS:
                    $this->brands($messageData, $operation);
                    break;
                case self::USERS:
                    $this->users($messageData, $operation);
                    break;
                case self::COMPANIES:
                    $this->companies($messageData, $operation);
                    break;
                case self::PERMISSIONS:
                    $this->permissions($messageData, $operation);
                    break;
                case self::ROLES:
                    $this->roles($messageData, $operation);
                    break;
                case self::POSITIONS:
                    $this->positions($messageData, $operation);
                    break;
                case self::USER_COMPANIES:
                    $this->userCompanies($messageData, $operation);
                    break;
            }
        } catch (Throwable $exception) {
            $this->logError($exception, $messageBody);
            if (app()->isLocal()) {
                throw $exception;
            }
        }
    }

    private function brands(stdClass $message, string $operation): void
    {
        switch ($operation) {
            case self::CREATED:
            case self::UPDATED:
                $this->brandCreateOrUpdate($message);
                break;
            case self::DELETED:
                $this->brandDelete($message);
                break;
        }
    }

    private function users(stdClass $message, string $operation): void
    {
        switch ($operation) {
            case self::CREATED:
            case self::UPDATED:
                $this->userCreateOrUpdate($message);
                break;
            case self::DELETED:
                $this->userDelete($message);
                break;
        }
    }

    private function positions(stdClass $message, string $operation): void
    {
        switch ($operation) {
            case self::CREATED:
                $this->positionCreateOrUpdate($message);
                break;
            case self::UPDATED:
                $this->positionCreateOrUpdate($message);
                break;
            case self::DELETED:
                $this->positionDelete($message);
                break;
        }
    }

    private function userCompanies(stdClass $message, string $operation): void
    {
        switch ($operation) {
            case self::CREATED:
                $this->userCompaniesCreateOrUpdate($message);
                break;
            case self::UPDATED:
                $this->userCompaniesCreateOrUpdate($message);
                break;
            case self::DELETED:
                $this->userCompaniesDelete($message);
                break;
        }
    }

    private function companies(stdClass $message, string $operation): void
    {
        switch ($operation) {
            case self::CREATED:
            case self::UPDATED:
                $this->companyCreateOrUpdate($message);
                break;
            case self::DELETED:
                $this->companyDelete($message);
                break;
        }
    }

    private function permissions(stdClass $message, string $operation): void
    {
        switch ($operation) {
            case self::SUPERVISOR_BROKERS_UPDATED:
                $this->permissionSupervisorBrokersUpdated($message);
                break;
        }
    }

    private function roles(stdClass $message, string $operation): void
    {
        switch ($operation) {
            case self::CREATED:
                $this->roleCreateOrUpdate($message);
                break;
            case self::UPDATED:
                $this->roleCreateOrUpdate($message);
                break;
            case self::DELETED:
                $this->roleDelete($message);
                break;
        }
    }
}

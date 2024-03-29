<?php

namespace BildVitta\SpHub\Console\Commands\Messages\Resources;

use BildVitta\SpHub\Console\Commands\Messages\Resources\Helpers\CompanyHelper;
use BildVitta\SpHub\Console\Commands\Messages\Resources\Helpers\CompanyLinksHelper;
use BildVitta\SpHub\Console\Commands\Messages\Resources\Helpers\LogHelper;
use BildVitta\SpHub\Console\Commands\Messages\Resources\Helpers\PermissionHelper;
use BildVitta\SpHub\Console\Commands\Messages\Resources\Helpers\PositionsHelper;
use BildVitta\SpHub\Console\Commands\Messages\Resources\Helpers\UserHelper;
use PhpAmqpLib\Message\AMQPMessage;
use stdClass;
use Throwable;

class MessageProcessor
{
    use LogHelper;
    use UserHelper;
    use CompanyHelper;
    use PermissionHelper;
    use PositionsHelper;
    use CompanyLinksHelper;

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
    public const POSITIONS = 'positions';

    /**
     * @var string
     */
    public const USER_COMPANIES = 'user_companies';

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

    /**
     * @param AMQPMessage $message
     * @return void
     */
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
                case self::USERS:
                    $this->users($messageData, $operation);
                    break;
                case self::COMPANIES:
                    $this->companies($messageData, $operation);
                    break;
                case self::PERMISSIONS:
                    $this->permissions($messageData, $operation);
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

    /**
     * @param stdClass $message
     * @param string $operation
     * @return void
     */
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

    /**
     * @param stdClass $message
     * @param string $operation
     * @return void
     */
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

    /**
     * @param stdClass $message
     * @param string $operation
     * @return void
     */
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

    /**
     * @param stdClass $message
     * @param string $operation
     * @return void
     */
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

    /**
     * @param stdClass $message
     * @param string $operation
     * @return void
     */
    private function permissions(stdClass $message, string $operation): void
    {
        switch ($operation) {
            case self::SUPERVISOR_BROKERS_UPDATED:
                $this->permissionSupervisorBrokersUpdated($message);
                break;
        }
    }
}

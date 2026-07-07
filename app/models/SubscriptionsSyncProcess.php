<?php
declare(strict_types=1);

use Phalcon\Mvc\Model;

class SubscriptionsSyncProcess extends Model
{
    public string $status;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public function initialize(): void
    {
        $this->setSource('subscriptions_sync_processes');
    }

    public function setStatus(SubscriptionsSyncProcessStatus $status): void
    {
        $this->status = $status->value;
    }

    public function getStatus(): ?SubscriptionsSyncProcessStatus
    {
        return SubscriptionsSyncProcessStatus::tryFrom($this->status);
    }
}
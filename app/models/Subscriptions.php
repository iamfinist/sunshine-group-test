<?php
declare(strict_types=1);

use Phalcon\Mvc\Model;

class Subscriptions extends Model
{
    public string $customer_id;
    public string $status;
    public string $created;
    public ?string $start_date;
    public ?string $cancel_at;
    public ?string $canceled_at;
    public ?string $ended_at;

    public function initialize(): void
    {
        $this->setSource('subscriptions');
    }
}
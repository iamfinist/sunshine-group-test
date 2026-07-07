<?php
declare(strict_types=1);

enum SubscriptionsSyncProcessStatus: string
{
    case Created = 'created';
    case InProgress = 'in_progress';
    case Error = 'error';
    case Completed = 'completed';
}
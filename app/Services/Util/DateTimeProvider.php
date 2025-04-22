<?php

namespace App\Services\Util;

use App\Contracts\Services\Util\DateTimeProviderInterface;

class DateTimeProvider implements DateTimeProviderInterface
{
    public function getCurrentDateTime(): string
    {
        return date('Y-m-d H:i:s');
    }
}

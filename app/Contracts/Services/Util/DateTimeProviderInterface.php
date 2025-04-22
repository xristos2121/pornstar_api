<?php

namespace App\Contracts\Services\Util;

interface DateTimeProviderInterface
{
    public function getCurrentDateTime(): string;
}

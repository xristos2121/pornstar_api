<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesTransactions
{
    protected function executeInTransaction(callable $operation, array $errorContext = []): mixed
    {
        DB::beginTransaction();
        try {
            $result = $operation();
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::warning(
                'Transaction failed: ' . $e->getMessage(),
                array_merge($errorContext, ['error' => $e->getMessage()])
            );
            return null;
        }
    }
}

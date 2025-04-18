<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PornstarAlias extends Model
{
    use HasFactory;

    protected $fillable = [
        'pornstar_id',
        'alias'
    ];

    public function pornstar(): BelongsTo
    {
        return $this->belongsTo(Pornstar::class);
    }
}

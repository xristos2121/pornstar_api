<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class PornstarAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'pornstar_id',
        'tattoos',
        'piercings',
        'breast_size',
        'breast_type',
        'orientation',
        'gender',
        'age'
    ];

    protected $casts = [
        'tattoos' => 'boolean',
        'piercings' => 'boolean',
        'age' => 'integer'
    ];

    public function pornstar(): BelongsTo
    {
        return $this->belongsTo(Pornstar::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Thumbnail extends Model
{
    use HasFactory;

    protected $fillable = [
        'pornstar_id',
        'height',
        'width',
        'type'
    ];

    protected $casts = [
        'height' => 'integer',
        'width' => 'integer'
    ];

    public function pornstar(): BelongsTo
    {
        return $this->belongsTo(Pornstar::class);
    }

    public function urls(): HasMany
    {
        return $this->hasMany(ThumbnailUrl::class);
    }
}

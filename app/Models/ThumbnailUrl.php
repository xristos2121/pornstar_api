<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThumbnailUrl extends Model
{
    use HasFactory;

    protected $fillable = [
        'thumbnail_id',
        'url',
        'cached_path'
    ];

    public function thumbnail(): BelongsTo
    {
        return $this->belongsTo(Thumbnail::class);
    }
}

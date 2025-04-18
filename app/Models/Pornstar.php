<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pornstar extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'name',
        'license',
        'wl_status',
        'link',
    ];

    protected $casts = [
        'wl_status' => 'boolean',
        'external_id' => 'integer',
    ];

    public function attributes(): HasOne
    {
        return $this->hasOne(PornstarAttribute::class);
    }

    public function stats(): HasOne
    {
        return $this->hasOne(PornstarStat::class);
    }

    public function aliases(): HasMany
    {
        return $this->hasMany(PornstarAlias::class);
    }

    public function thumbnails(): HasMany
    {
        return $this->hasMany(Thumbnail::class);
    }

    public function hairColors(): BelongsToMany
    {
        return $this->belongsToMany(HairColor::class, 'pornstar_hair_colors');
    }

    public function ethnicities(): BelongsToMany
    {
        return $this->belongsToMany(Ethnicity::class, 'pornstar_ethnicities');
    }
}

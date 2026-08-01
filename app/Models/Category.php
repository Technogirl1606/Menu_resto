<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\Item;


class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'position', 'is_active', 'available_from', 'available_to'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function isCurrentlyAvailable(): bool
{
    if (!$this->available_from || !$this->available_to) {
        return true;
    }

    $now = now()->format('H:i');
    $from = substr($this->available_from, 0, 5);
    $to = substr($this->available_to, 0, 5);

    return $from <= $to
        ? ($now >= $from && $now <= $to)
        : ($now >= $from || $now <= $to);
}

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function items()
    {
        return $this->hasMany(Item::class)->orderBy('position');
    }
}
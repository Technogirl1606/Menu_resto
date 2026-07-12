<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;

class Item extends Model
{
     use HasFactory;
 
    protected $fillable = [
        'category_id', 'name', 'description', 'price', 'image_path', 'is_available', 'position',
    ];
 
    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];
 
    // Ajoute automatiquement "image_url" quand le modèle est converti en JSON (utile pour Vue)
    protected $appends = ['image_url'];
 
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
 
    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::url($this->image_path) : null;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'description', 'cover_image',
        'price_cents', 'original_price_cents', 'is_on_sale', 'discount_percent',
        'has_pdf', 'has_epub', 'has_physical', 'stock',
        'rating_avg', 'rating_count', 'pages', 'language',
        'publisher', 'published_at',
        'is_featured', 'is_bestseller', 'is_new_arrival'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_on_sale' => 'boolean',
        'has_pdf' => 'boolean',
        'has_epub' => 'boolean',
        'has_physical' => 'boolean',
        'is_featured' => 'boolean',
        'is_bestseller' => 'boolean',
        'is_new_arrival' => 'boolean',
    ];

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class);
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}

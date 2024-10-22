<?php

namespace App\Model\Common;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = "products";

    public function scopePublished($query)
    {
        return $query->where('status', 1);
    }

    public function categories()
    {
        return $this->morphToMany("App\Model\Common\Category", "categoryable");
    }

    public function tags()
    {
        return $this->morphToMany("App\Model\Common\Tag", "taggable");
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class)->withTimestamps();
    }

    public function attributeProduct()
    {
        return $this->hasMany(AttributeProduct::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function units()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function user()
    {
        return $this->belongsTo("App\User", "created_by");
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getStarRatingAttribute()
    {
        $count = $this->reviews()->count();
        if ($count === 0) {
            return 0;
        }
        $starCountSum = $this->reviews()->sum('rating');
        return $starCountSum / $count;
    }

    // Accessor for reviewCount
    public function getReviewCountAttribute()
    {
        return $this->reviews()->count();
    }

    // Accessor for reviewsRating
    public function getReviewsRatingAttribute()
    {
        $reviewData = $this->reviews()->selectRaw('SUM(rating) as totalRating, COUNT(rating) as reviewsCount')->first();

        if ($reviewData && $reviewData->reviewsCount > 0) {
            return $reviewData->totalRating / $reviewData->reviewsCount;
        }
        return null; // Return null if there are no reviews
    }
}

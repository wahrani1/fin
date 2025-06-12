<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Site extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'images', 'videos', 'category', 'governorate_id', 'era_id'];

    public const CATEGORIES = ['Pyramid', 'Church', 'Mosque', 'Temple', 'antiquity', 'Cemeteries', 'Palaces'];

    /*     Validation for category
    public static function validateCategory(string $category): bool
    {
        return in_array($category, self::CATEGORIES, true);
     }*/
    public function era(): BelongsTo
    {
        return $this->belongsTo(Era::class, 'era_id');
    }
    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }
    public function images(): HasMany
    {
        return $this->hasMany(ArticleImage::class);
    }
    public function comments(): HasMany
    {
        return $this->hasMany(ArticleComment::class);
    }
    public function ratings(): HasMany
    {
        return $this->hasMany(ArticleRating::class);
    }
    public static function rules()
    {
        return [
            'name' => ['required', 'string', 'min:3'],
            'description' => ['required', 'string'],
            'category' => ['required', 'string', 'in:Pyramid,Church,Mosque,Temple,antiquity,Cemeteries,Palaces'],
            'images' => 'image',
            'era_id' => ['required', 'in:1,2,3']
        ];
    }


}

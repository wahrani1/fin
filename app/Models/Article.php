<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'category', 'governorate_id', 'era_id', 'user_id'];

    public const CATEGORIES = ['Pyramid','Church','Mosque','Temple','antiquity','Cemeteries','Palaces'];

    public function era(): BelongsTo
    {
        return $this->belongsTo(Era::class);
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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function ratings(): HasMany
    {
        return $this->hasMany(ArticleRating::class);
    }

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3'],
            'description' => ['required', 'string'],
            'category' => ['required', 'string', 'in:Pyramid,Church,Mosque,Temple,antiquity,Cemeteries,Palaces'],
            'images.*' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'era_id' => ['required', 'exists:eras,id'],
            'governorate_id' => ['sometimes', 'nullable', 'exists:governorates,id'],
            'user_id' => ['sometimes', 'nullable', 'exists:users,id'],
        ];
    }
}

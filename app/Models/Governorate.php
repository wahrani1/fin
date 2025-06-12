<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Governorate extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image', 'brief', 'description', 'visit_count'];

    protected $casts = [
        'visit_count' => 'integer',
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}

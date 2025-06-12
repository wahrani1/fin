<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertifiedResearcher extends Model
{
    // Make sure this matches your actual table name
    protected $table = 'certified_researchers';

    protected $fillable = ['user_id', 'file', 'major', 'status', 'rejection_reason'];

    protected $attributes = [
        'status' => 'pending', // Default status is pending
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

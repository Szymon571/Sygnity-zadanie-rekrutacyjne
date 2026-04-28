<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WatchedCurrency extends Model
{

    protected $fillable = [
        'currency_code',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}

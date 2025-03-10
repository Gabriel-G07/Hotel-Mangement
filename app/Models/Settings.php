<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Settings extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'theme',
        'screen_timeout',
        'font_style',
        'font_size',
        'notifications_enabled',
        'language',
        'timezone',
        'two_factor_auth',
        'date_format',
        'time_format',
    ];

    protected $table = 'user_settings';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

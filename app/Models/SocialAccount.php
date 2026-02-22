<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SocialAccount extends Model
{
    use HasFactory;

    protected $table = 'social_accounts';

    protected $fillable = [
        'loja_id',
        'provider',
        'provider_id',
        'token',
        'refresh_token',
        'expires_at',
        'meta_data'
    ];

    protected $casts = [
        'meta_data' => 'array',
        'expires_at' => 'datetime'
    ];

    public function loja()
    {
        return $this->belongsTo(Loja::class, 'loja_id');
    }
}

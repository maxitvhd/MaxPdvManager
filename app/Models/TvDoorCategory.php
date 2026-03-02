<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TvDoorCategory extends Model
{
    use HasFactory;

    protected $table = 'tv_door_categories';

    protected $fillable = [
        'loja_id',
        'name'
    ];

    public function loja()
    {
        return $this->belongsTo(Loja::class);
    }

    public function media()
    {
        return $this->hasMany(TvDoorMedia::class, 'category_id');
    }
}

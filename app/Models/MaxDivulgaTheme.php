<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaxDivulgaTheme extends Model
{
    use HasFactory;

    protected $table = 'max_divulga_themes';

    protected $fillable = [
        'name',
        'identifier',
        'path',
        'is_active'
    ];
}

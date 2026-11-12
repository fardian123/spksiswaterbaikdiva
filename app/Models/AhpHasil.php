<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AhpHasil extends Model
{
    protected $table = 'ahp_hasil';
    protected $guarded = [];

    protected $casts = [
        'detail'        => 'array',
        'is_konsisten'  => 'boolean',
        'diterapkan_at' => 'datetime',
    ];
}

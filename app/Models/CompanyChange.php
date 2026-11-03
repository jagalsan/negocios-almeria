<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyChange extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['before' => 'array', 'after' => 'array', 'reviewed_at' => 'datetime'];
    }
}

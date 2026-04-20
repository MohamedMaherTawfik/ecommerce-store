<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Addresses extends Model
{
    protected $table = 'addresses';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

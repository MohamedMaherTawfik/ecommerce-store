<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItems extends Model
{
    protected $table = 'cart_items';
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Products::class);
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
}
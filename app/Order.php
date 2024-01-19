<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Product;

class Order extends Model
{
    protected $fillable = ['id', 'user_id', 'payment_id','total', 'status'];
    public function user(){

        return $this -> belongsTo(User::class);

    }
    public function products()
    {
        return $this->belongsToMany(Product::class)->withTimestamps()->withPivot('quantity');
    }
}

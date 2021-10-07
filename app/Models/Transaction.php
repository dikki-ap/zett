<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'users_id',
        'address',
        'payment',
        'total_price',
        'shipping_price',
        'status'
    ];

    //Relasi ke tabel users
    public function user(){
        $this->belongsTo(User::class, 'users_id', 'id'); // 'foreignKey' , 'originId'
    }

    //Relasi ke tabel transaction_items
    public function items(){
        return $this->hasMany(TransactionItem::class, 'transactions_id', 'id');
    }
}

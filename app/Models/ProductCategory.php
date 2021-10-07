<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductCategory extends Model
{
    use HasFactory, SoftDeletes;

       /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name'
    ];

    //Relasi ke tabel products (O T O)
    public function products(){
        return $this->hasMany(Product::class, 'categories_id', 'id'); // 'foreignKey' , 'localKey'
    }
}

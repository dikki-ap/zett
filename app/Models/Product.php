<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

      /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'categories_id',
        'tags'
    ];

    // Relasi ke tabel product_galleries (O T M)
    public function galleries(){
        return $this->hasMany(ProductGallery::class, 'products_id', 'id'); // 'foreignKey' , 'localKey'
    }

    // Relasi ke tabel product_categories (O T O)
    public function category(){
        return $this->belongsTo(ProductCategory::class, 'categories_id', 'id');
    }
}

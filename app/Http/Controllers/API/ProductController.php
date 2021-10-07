<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    // Semua Product akan di handle oleh 1 controller menggunakan function ini
    public function all(Request $request){
        $id = $request->input('id');
        $limit = $request->input('limit', 6);
        $name = $request->input('id');
        $description = $request->input('description');
        $tags = $request->input('tags');
        $categories = $request->input('categories');

        $price_from = $request->input('price_from');
        $price_to = $request->input('price_to');

        //Mengambil data individual berdasarkan id dari API
        if($id){
            $product = Product::with(['category','galleries'])->find($id);

            //Jika data ada, maka akan dikembalikan ke API serta message, jika tidak ada hal yang sama akan dilakukan tetapi mengembalikan nilai NULL
            if($product){
                return ResponseFormatter::success(
                    $product, 'Data produk berhasil diambil'
                );
            }else{
                return ResponseFormatter::error(
                    null, 'Data produk tidak ada', 404
                );
            }
        }

        $product = Product::with(['category','galleries']);

        //mencari produk berdasarkan nama
        if($name){
            // menggunakan query LIKE
            $product->where('name', 'like', '%' . $name . '%');
        }

        if($description){
            // menggunakan query LIKE
            $product->where('description', 'like', '%' . $description . '%');
        }

        if($tags){
            // menggunakan query LIKE
            $product->where('tags', 'like', '%' . $tags . '%');
        }

        if($categories){
            $product->where('categories', $categories);
        }

        if($price_from){
            $product->where('price', '>=', $price_from);
        }

        if($price_to){
            $product->where('price', '<=', $price_to);
        }

        return ResponseFormatter::success(
            $product->paginate($limit), 'Data produk berhasil diambil'
        );
        
    }
}

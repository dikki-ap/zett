<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;

class ProductCategoryController extends Controller
{
    public function all(Request $request){
        $id = $request->input('id');
        $limit = $request->input('limit', 6);
        $name = $request->input('id');
        $show_product = $request->input('show_product');

        //Mengambil data individual berdasarkan id dari API
        if($id){
            $category = ProductCategory::with(['products'])->find($id);

            //Jika data ada, maka akan dikembalikan ke API serta message, jika tidak ada hal yang sama akan dilakukan tetapi mengembalikan nilai NULL
            if($category){
                return ResponseFormatter::success(
                    $category, 'Data kategori berhasil diambil'
                );
            }else{
                return ResponseFormatter::error(
                    null, 'Data kategori tidak ada', 404
                );
            }
        }

        $category = ProductCategory::query();

        //mencari produk berdasarkan nama
        if($name){
            // menggunakan query LIKE
            $category->where('name', 'like', '%' . $name . '%');
        }

        if($show_product){
            $category->with('products');
        }

        return ResponseFormatter::success(
            $category->paginate($limit), 'Data list kategori berhasil diambil'
        );

    }
}
